<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 * 
 **/

namespace   Splash\Models\Objects;

use Splash\Core\SplashCore      as Splash;

/**
 * @abstract    This class implements Intelligent Parser to Access Objects Data
 */
trait IntelParserTrait
{
    
    use FieldsFactoryTrait;
    use UpdateFlagTrait;
    
    //====================================================================//
    // General Class Variables	
    //====================================================================//
    
    /**
     * Set Operations Input Buffer
     * 
     * @abstract This variable is used to store Object Array during Set Operations
     *              Each time a field is imported, unset it from this buffer 
     *              to control all fields were imported at the end of Set Operation
     * @var ArrayObject
     */
    protected   $In            = Null;
    
    /**
     * Get Operations Output Buffer
     * 
     * @abstract This variable is used to store Object Array during Get Operations
     * @var ArrayObject
     */
    protected   $Out            = Null;
    
    /**
     * Work Object Class 
     * 
     * @abstract This variable is used to store current working Object during Set & Get Operations
     * @var mixed
     */
    protected   $Object         = Null;
    
      
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     * {@inheritdoc}
    */
    public function Fields()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);    
        
        foreach ($this->identifyFunctions("build") as $Method) {
            $this->{$Method}();
        }
        
        //====================================================================//
        // Publish Fields
        return $this->FieldsFactory()->Publish();
    }    
    
    /**
     * {@inheritdoc}
    */
    public function Get( $Id = Null , $List = Null )
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);   
        //====================================================================//
        // Init Reading
        $this->In = $List;
        //====================================================================//
        // Load Object 
        $this->Object   =   $this->Load($Id);
        if ( !is_object($this->Object) )   {
            return False;
        }
        //====================================================================//
        // Init Response Array 
        $this->Out  =   array( "id" => $Id );
        //====================================================================//
        // Run Through All Requested Fields
        //====================================================================//
        $Fields = is_a($this->In, "ArrayObject") ? $this->In->getArrayCopy() : $this->In;        
        foreach ($Fields as $Key => $FieldName) {
            //====================================================================//
            // Read Requested Fields            
            foreach ($this->identifyFunctions("get") as $Method) {
                $this->{$Method}($Key,$FieldName);
            }            
        }        
        //====================================================================//
        // Verify Requested Fields List is now Empty => All Fields Read Successfully
        if ( count($this->In) ) {
            foreach ($this->In as $FieldName) {
                Splash::Log()->Err("ErrLocalWrongField",__CLASS__,__FUNCTION__, $FieldName);
            }
            return False;
        }        
        //====================================================================//
        // Return Data
        //====================================================================//
//        if ( SPLASH_DEBUG ) {
//            Splash::Log()->www("Read Data", $this->Out);
//        } 
        return $this->Out; 
    }  
    
    /**
     * {@inheritdoc}
    */
    public function Set( $Id = Null , $List = Null )
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);
//        if ( SPLASH_DEBUG ) {
//            Splash::Log()->www("Write Data", $List);
//        } 
        //====================================================================//
        // Init Reading
        $this->In           =   $List;
        $this->isUpdated();
        //====================================================================//
        // Init Object
        if( $Id ) {
            $this->Object   =   $this->Load($Id);
        } else {
            $this->Object   =   $this->Create();
        }
        if ( !is_object($this->Object) )   {
            return False;
        }
        //====================================================================//
        // Run Throw All Requested Fields
        //====================================================================//
        $Fields = is_a($this->In, "ArrayObject") ? $this->In->getArrayCopy() : $this->In;        
        foreach ($Fields as $FieldName => $Data) {
            //====================================================================//
            // Write Requested Fields
            foreach ($this->identifyFunctions("set") as $Method) {
                $this->{$Method}($FieldName,$Data);
            }              
        }
        //====================================================================//
        // Verify Requested Fields List is now Empty => All Fields Writen Successfully
        if ( count($this->In) ) {
            foreach ($this->In as $FieldName => $Data) {
                Splash::Log()->Err("ErrLocalWrongField",__CLASS__,__FUNCTION__, $FieldName);
            }
            return False;
        }        
        
        return $this->Update($this->isToUpdate());
    }      
    
    //====================================================================//
    //  TOOLING FUNCTION
    //====================================================================//

    /**
     * @abstract    Identify Generic Functions
     * 
     * @return      self
     */
    public function identifyFunctions($Prefix)
    {
        $Result = array();
        foreach (get_class_methods(__CLASS__) as $MethodName) {
            if ( strpos($MethodName, $Prefix ) !== 0 ){
                continue;
            }
            if ( strpos($MethodName, "Fields" ) === False ){
                continue;
            }
            $Result[]   =   $MethodName;
        }
        return $Result;
    } 
    
    /**
     * @abstract    Check Required Fields
     * 
     * @return      bool
     */
    public function verifyRequiredFields()
    {
        foreach ($this->Fields() as $Field) {
            // Field is NOT required
            if ( !$Field["required"] ){
                continue;
            }
            
            // Field is Required but not available
            if ( !$this->verifyRequiredFieldIsAvailable($Field["id"]) ) {
                return Splash::Log()->Err("ErrLocalFieldMissing",__CLASS__,__FUNCTION__, $Field["name"] . "(" . $Field["id"] . ")");
            }
        }
        return True;
    }     

    /**
     * @abstract    Check Required Fields
     * 
     * @return      bool
     */
    private function verifyRequiredFieldIsAvailable( $FieldId )
    {

        // Detect List Field Names
        if ( !method_exists($this,"Lists") || !self::Lists()->ListName($FieldId)) {
            

            //====================================================================//
            // Simple Field is Required but not available
            if ( empty($this->In[$FieldId]) ) {
                    return False;
            }
            return True;
        }
        

        //====================================================================//
        // List Field is required 
        $ListName   = self::Lists()->ListName($FieldId);
        $FieldName  = self::Lists()->FieldName($FieldId);

        // Check List is available
        if ( empty($this->In[$ListName]) ) {
            return False;
        }                
        // list is a List... 
        if ( !is_array($this->In[$ListName]) && !is_a($this->In[$ListName], "ArrayObject") ) {
            return False;
        }     
        
        // Check Field is Available
        foreach ($this->In[$ListName] as $Item) {
            if ( empty($Item[$FieldName]) ) {
                    return False;
            }
        }

        return True;
    }     
    
}
