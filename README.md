# LthrtEntityBundle
Generic Crud and JSON representations for entities for Doctrine/Symfony Bundles

This bundle uses php traits and annotations to build as much as possible automatically.
It uses php's __get, __set(), __call to create the following behavior:

```
    private $field
```
is accessed by 
```
    $entity->field;
    $entity->field = 'newValue';
```
unless the corresponding getField() or setField($value) in which case those methods will be used instead.

Private fields may be marked:
``` 
    private $_noAccess; // preface name with underscore
```
may not be accessed except within the entity itself.  

The various traits are meant to be included as
```
class item {
    use \Lthrt\EntityBundle\Entity\ActiveTrait;         // e.g. a common trait used in databases
    use \Lthrt\EntityBundle\Entity\DoctrineEntityTrait; // magic behavior
                                                        // gives above behavior and
                                                        // addField(), removeField(), and clearField()
                                                        // for use with doctrine collections

    // additional user-provided definitions       

    /**
     * @ORM\OneToMany(targetEntity="otherClass", mappedBy="item")
     */
    private $collection;

    public function __construct()
    {
        $this->collection = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

Entities that can be routed to the generic controllers are registered in app/config/aliases.yml:

app/config/aliases.yml
```
class_aliases:
    entity:  Namespace\entity.php
    entity2: Namespace\entity2.php
```

To turn on full logging, each entity should 
    implement \Lthrt\EntityBundle\Entity\EntityLog

For just updated time stamps/created
    implement \Lthrt\EntityBundle\Entity\EntityLedger

To log partial records (in a controller):
    $this->get('lthrt.entity.partial.logger')->partial($entity);

Using the LoggingDisabledTrait adds a field: $loggingDisabled.  
If this is set to true, changes will not be logged.  This is meant to be used temporarily.
Use the annotation for a more permanent fix;

Logging calls JsonSerialize method in the entity class code if it exists,
otherwise it reads Doctrine Metadata.

When metadata is read to determine json representation of changes, an annotation exists 
for disabling logging on a single property, to be placed in the doc-block immediately 
above the property, eg:
```
    /**                
     * // double star is important
     *
     * // Must be an ORM column to log (or disable)
     *
     * @var string
     *
     * @Lthrt\EntityBundle\Annotation\NoLogThisField(active=true)
     * @ORM\Column(name="no_log", type="string", length=255)
     */
     private $noLoggingThisField
```
---
Routes may be imported:

app/config/routing.yml:
```
    lthrt_entity:
        resource: "@LthrtEntityBundle/Controller/"
        type:     annotation
```

which provide api-like interfaces to get JSON for entities directly:

    {class} is the class specified in aliases.yml:

    log is the complete set of logs for the entity
    entity_log                 GET              ANY      ANY    /_{class}/log/{id}       

    The following routes query the current json representation for one or more entities.
    Multiple ids are underscore or comma delimited

    entities_json              GET              ANY      ANY    /_{class}/json                            
    entity_json                GET              ANY      ANY    /_{class}/json/{id}

A quick form via this api with a json field and a submit button
    entity_create              POST             ANY      ANY    /_{class}/create                         
    entity_mod                 PUT              ANY      ANY    /_{class}/mod/{id}                         
    entity_new                 GET              ANY      ANY    /_{class}/new                            
    entity_edit                GET              ANY      ANY    /_{class}/edit/{id}

for example, from the above /_entity2/json