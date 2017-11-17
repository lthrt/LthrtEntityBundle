<?php

namespace Lthrt\EntityBundle\Model;

class ClassVerifier
{
    private $aliases; // shortcuts for namespaces configured in app/config/aliases.yml
    private $em;
    private $logger;

    public function __construct(
        $em,
        $aliases,
        $logger = null
    ) {
        foreach ($aliases as $alias => $class) {
            $this->aliases[$alias] = $class;
        }
        $this->em     = $em;
        $this->logger = $logger;
    }

    public function verifyClass($class)
    {
        if (isset($this->aliases[$class])) {
            $class = $this->aliases[$class];
        } else {
        }

        $metadataFactory = $this->em->getMetadataFactory();
        // var_dump(array_map(function ($m) {return $m->name;}, $metadataFactory->getAllMetadata()));
        $error = null;
        try {
            $metadata = $metadataFactory->getMetadataFor($class);
        } catch (\Exception $ex) {
            $error = __FILE__ . ": alias for '$class' not registered app/config/aliases.yml file";
        }

        if ($error) {
            if ($this->logger) {
                $this->logger->error($error);
            }

            return false;
        } else {
            return $class;
        }
    }

    public function classAssociations($class)
    {
        if ($this->verifyClass($class)) {
            $metadataFactory = $this->em->getMetadataFactory();
            $metadata        = $metadataFactory->getMetadataFor($this->verifyClass($class));
            return $metadata->associationMappings;
        } else {
            return null;
        }

    }
}
