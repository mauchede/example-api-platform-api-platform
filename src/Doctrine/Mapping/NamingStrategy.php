<?php

declare(strict_types=1);

namespace App\Doctrine\Mapping;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

class NamingStrategy extends UnderscoreNamingStrategy
{
    /**
     * {@inheritdoc}
     */
    public function classToTableName($className): string
    {
        $parts = explode('_', parent::classToTableName($className));

        $last = array_pop($parts);
        $last = Inflector::pluralize($last);
        $parts[] = $last;

        return implode('_', $parts);
    }

    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null): string
    {
        return parent::classToTableName($entityName) . '_' . ($referencedColumnName ?: $this->referenceColumnName());
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null): string
    {
        return parent::classToTableName($sourceEntity) . '_' . $this->underscore($propertyName);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function underscore($string): string
    {
        return strtolower(preg_replace('`(?<=[a-z])([A-Z])`', '_$1', $string));
    }
}
