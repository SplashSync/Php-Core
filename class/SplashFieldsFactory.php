<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    This Class is a Generator for Objects Fields Definition
 * @author      B. Paquier <contact@splashsync.com>
 */

class SplashFieldsFactory 
{
    /**
     * Default Field Definition Resolver Array
     * 
     * @var array
     */
    private static $FIELDS_DEFAULTS = array(
        //==============================================================================
        //      GENERAL FIELD PROPS
        "required"  =>  Null,                   //  Field is Required to Create a New Object (Bool)
        "type"      =>  Null,                   //  Field Fomat Type Name
        "id"        =>  Null,                   //  Field Object Unique Identifier
        "name"      =>  Null,                   //  Field Humanized Name (String)
        "desc"      =>  Null,                   //  Field Description (String)
        //==============================================================================
        //      ACCES PROPS
        "read"      =>  True,                   //  Field is Readable (Bool)
        "write"     =>  True,                   //  Field is Writable (Bool)
        "inlist"    =>  False,                  //  Field is Available in Object List Response (Bool)
        //==============================================================================
        //      SCHEMA.ORG IDENTIFICATION
        "itemprop"  =>  Null,                   //  Field Unique Schema.Org "Like" Property Name
        "itemtype"  =>  Null,                   //  Field Unique Schema.Org Object Url
        "tag"       =>  Null,                   //  Field Unique Linker Tags (Self-Generated)
        //==============================================================================
        //      DATA SPECIFIC FORMATS PROPS
        "choices"   =>  array(),                //  Possible Values used in Editor & Debugger Only  (Array)
        //==============================================================================
        //      DATA LOGGING PROPS
        "log"       =>  False,                  //  Field is To Log (Bool)
        //==============================================================================
        //      DEBUGGER PROPS
        "asso"      =>  array(),                //  Associated Fields. Fields to Generate When Generating Random value of this field.
        "notest"    =>  False,                  //  Do No Perform Tests for this Field
    );    
    
    //====================================================================//
    // Data Storage 

    /**
     *      @abstract   Empty Template Object Field Storage
     *      @var        Array
     */     
    private $empty;

    
    /**
     *      @abstract   New Object Field Storage
     *      @var        ArrayObject
     */     
    private $new;
    
    /**
     *      @abstract   Object Fields List Storage
     *      @var        Array
     */     
    private $fields;
    
    /**
     *      @abstract     Initialise Class
     *      @return         int           <0 if KO, >0 if OK
     */
    function __construct()
    {
        
        //====================================================================//
        // Initialize Data Storage
        $this->new            = Null;          
        $this->fields         = array(); 
        
        //====================================================================//
        // Initialize Template Field
        $this->empty          = self::$FIELDS_DEFAULTS;
        
        return True;
    }

//====================================================================//
//  FIELDS :: DATA TYPES DEFINITION
//====================================================================//

    /**
     *  @abstract   Create a new Field Definition with default parameters 
     *               
     *  @param      string      $type       Standard Data Type (Refer osws.inc.php)
     *  @param      string      $id         Local Data Identifier (Shall be unik on local machine)
     *  @param      string      $name       Data Name (Will Be Translated by OsWs if Possible)
     *  @return     int                     <0 if KO, >0 if OK
     */
    public function Create($type,$id = Null, $name = Null)
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (!empty($this->new)) {    
            $this->Commit();  
        }
        
        //====================================================================//
        // Unset Current
        unset($this->new);
        
        //====================================================================//
        // Create new empty field
        $this->new          =   new ArrayObject($this->empty,  ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Set Field Type
        $this->new->type    =   $type;
        //====================================================================//
        // Set Field Identifier
        if ( !is_null($id) ) {
            $this->Identifier($id);
        } 
        //====================================================================//
        // Set Field Name
        if ( !is_null($name) ) {
            $this->Name($name);
        } 
        
        return $this;
    } 
    
    /**
     *  @abstract   Set Current New Field Identifier
     * 
     *  @param      string      $id         Local Data Identifier (Shall be unik on local machine)
     * 
     *  @return     SplashFieldFactory
     */
    public function Identifier($id)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->id    = $id; 
        }
        
        return $this;      
    }  
    
    /**
     *  @abstract   Update Current New Field set as it inside a list 
     * 
     *  @param      string      $ListName         Name of List
     * 
     *  @return     SplashFieldFactory
     */
    public function InList($ListName)
    {
        //====================================================================//
        // Safety Checks ==> Verify List Name Not Empty
        if (empty($ListName)) {    
            return $this; 
        }
        
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            
            //====================================================================//
            // Update New Field Identifier
            $this->new->id      =   $this->new->id . LISTSPLIT . $ListName; 
            //====================================================================//
            // Update New Field Type
            $this->new->type    =   $this->new->type . LISTSPLIT . SPL_T_LIST; 

        }
        
        return $this;
    }        
    
    /**
     *  @abstract   Set Current New Field Name (Translated)
     * 
     *  @param      string      $name       Data Name (Will Be Translated if Possible)
     * 
     *  @return     SplashFieldFactory
     */
    public function Name($name)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->name    = $name; 
            if ( empty($this->new->desc)) {
                $this->Description($name);
            }
        }
        
        return $this;      
    }  
    
    /**
     *  @abstract   Update Current New Field with descriptions (Translated)
     * 
     *  @param      string      $desc       Data Description (Will Be Translated if Possible)
     * 
     *  @return     SplashFieldFactory
     */
    public function Description($desc)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->desc    = Splash::Trans(trim($desc)); 
        }
        
        return $this;      
    }   
    
    /**
     *  @abstract   Update Current New Field set as Read Only Field 
     * 
     *  @return     SplashFieldFactory
     */
    public function ReadOnly()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->read    = True; 
            $this->new->write   = False; 
        }
        
        return $this;            
    }       
    
    /**
     *  @abstract   Update Current New Field set as Write Only Field 
     * 
     *  @return     SplashFieldFactory
     */
    public function WriteOnly()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->read    = False; 
            $this->new->write   = True; 
        }
        
        return $this;          
    }         
    
    /**
     *  @abstract   Update Current New Field set as required for creation 
     * 
     *  @return     SplashFieldFactory
     */
    public function isRequired()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->required  = True; 
        }
        
        return $this;        
    }  
    
    /**
     *  @abstract   Update Current New Field set list of associated fields
     * 
     *  @param      string                  Objects Fields Identifiers
     * 
     *  @return     SplashFieldFactory
     */
    public function Association()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            
            //====================================================================//
            // Field Clear Fields Associations
            if (!empty($this->new->asso))   {    
                unset($this->new->asso);  
            }
            
            //====================================================================//
            // Set New Field Associations
            if (!empty(func_get_args()))   {    
                $this->new->asso  = func_get_args();
            }
        }
        
        return $this;
    }      
    
    /**
     *  @abstract   Update Current New Field set as available in objects list 
     * 
     *  @return     SplashFieldFactory
     */
    public function isListed()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->inlist  = True; 
        }
        
        return $this;
    }        
    
    /**
     *  @abstract   Update Current New Field set as recommended for logging 
     * 
     *  @return     SplashFieldFactory
     */
    public function isLogged()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->log  = True; 
        }
        
        return $this;        
    }  
    
    /**
     *  @abstract   Update Current New Field set its meta informations for autolinking
     * 
     *  @param      string      $ItemType   Field Microdata Type Url
     *  @param      string      $ItemProp   Field Microdata Property Name
     * 
     *  @return     SplashFieldFactory
     */
    public function MicroData($ItemType,$ItemProp)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->itemtype            = $ItemType; 
            $this->new->itemprop            = $ItemProp; 
            $this->Tag($ItemProp . IDSPLIT . $ItemType);
        }
        
        return $this;
    }
    
    /**
     *  @abstract   Update Current New Field set its unik tag for autolinking
     * 
     *  @param      string      $Tag       Field Unik Tag
     * 
     *  @return     SplashFieldFactory
     */
    public function Tag($Tag)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->tag     = md5($Tag); 
        }
        
        return $this;
    }      
        
    /**
     *  @abstract   Update Current New Field set as not possible to test 
     * 
     *  @return     SplashFieldFactory
     */
    public function NotTested()
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->notest  = True; 
        }
        
        return $this;
    }     
    
    /**
     *  @abstract   Add Possible Choice to Current New Field Name (Translated)
     * 
     *  @param      array      $Choices      Possible Choice Array (Value => Decsription) 
     * 
     *  @return     SplashFieldFactory
     */
    public function AddChoices($Choices)
    {
        foreach ($Choices as $Value => $Description) {
            $this->AddChoice($Value, $Description);
        }
        return $this;      
    }  

    /**
     *  @abstract   Add Possible Choice to Current New Field Name (Translated)
     * 
     *  @param      string      $Value      Possible Choice Value 
     *  @param      string      $Desc       Choice Description for Display (Will Be Translated if Possible)
     * 
     *  @return     SplashFieldFactory
     */
    public function AddChoice($Value, $Description)
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {    
            Splash::Log()->Err("ErrFieldsNoNew");  
        } else {
            //====================================================================//
            // Update New Field structure
        $this->new->choices[]   = array( 
            "key"   =>  $Value,
            "value" =>  Splash::Trans(trim($Description))
                ); 
        }
        
        return $this;      
    }  
    
    /**
     *  @abstract   Verify Current New Field data 
     * 
     *  @return     bool
     */
    private function Verify()
    {
        //====================================================================//
        // If new Field is Empty
        if ( !isset($this->new) || empty($this->new)) {    
            return False;  
        }

        //====================================================================//
        // Verify - Field Type is Not Empty
        if (empty($this->new->type) || !is_string($this->new->type))     {   
            return Splash::Log()->Err("ErrFieldsNoType");   
        }

        //====================================================================//
        // Verify - Field Id is Not Empty
        if (empty($this->new->id) || !is_string($this->new->id))     {   
            return Splash::Log()->Err("ErrFieldsNoId");   
        }
        
        //====================================================================//
        // Verify - Field Name is Not Empty
        if (empty($this->new->name) || !is_string($this->new->name))   {   
            return Splash::Log()->Err("ErrFieldsNoName"); 
        }
        
        //====================================================================//
        // Verify - Field Desc is Not Empty
        if (empty($this->new->desc) || !is_string($this->new->desc))   {   
            return Splash::Log()->Err("ErrFieldsNoDesc"); 
        }

        return True;
    }       

    /**
     *  @abstract   Save Current New Field in list & Clean current new field 
     * 
     *  @return     bool
     */
    private function Commit()
    {
        //====================================================================//
        // Safety Checks
        if (empty($this->new)) {    
            return True;  
        }
            
        //====================================================================//
        // Create Field List
        if (empty($this->fields)) {
            $this->fields   = array();
        }

        //====================================================================//
        // Validate New Field
        if ( !$this->Verify() ) {
            unset($this->new);
            return False;
        } 
        
        //====================================================================//
        // Insert Field List
        $this->fields[] = $this->new;
        unset($this->new);
        
        return True;
    }       
    
    /**
     *  @abstract   Save Current New Field in list & Clean current new field 
     * 
     *  @return     int                     <0 if KO, >0 if OK
     */
    public function Publish()
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (!empty($this->new)) {    
            $this->Commit();  
        }
        
        //====================================================================//
        // Safety Checks
        if (empty($this->fields)) {    
            return Splash::Log()->Err("ErrFieldsNoList");  
        }
        //====================================================================//
        // Return fields List
        else { 
            $buffer = $this->fields;
            unset($this->fields);
            return $buffer;   
        }
        
        return False;
    }  
    
    /**
     *  @abstract   Seach for a Field by unik tag 
     * 
     *  @param      array       $List      Array Of Field definition 
     *  @param      string      $Tag       Field Unik Tag
     *  @return     mixed                  FALSE if KO, Field Definition array if OK
     */
    public function seachtByTag($List,$Tag)
    {
        //====================================================================//
        // Safety Checks
        if (!count($List))  {    return FALSE;  }
        if (empty($Tag))    {    return FALSE;  }
        //====================================================================//
        // Walk Through List and select by Tag
        foreach ($List as $field) {
            if ( $field["tag"] == $Tag ) { 
                return $field;
            }
        }        
        return FALSE;
    }       
    /**
     *  @abstract   Seach for a Field by id 
     * 
     *  @param      array       $List      Array Of Field definition 
     *  @param      string      $Id        Field Identifier
     *  @return     mixed                  FALSE if KO, Field Definition array if OK
     */
    public function seachtById($List,$Id)
    {
        //====================================================================//
        // Safety Checks
        if (!count($List))  {    return FALSE;  }
        if (empty($Id))     {    return FALSE;  }
        //====================================================================//
        // Walk Through List and select by Tag
        foreach ($List as $field) {
            if ( $field["id"] == $Id ) { 
                return $field;
            }
        }        
        return FALSE;
    }      
    
}




