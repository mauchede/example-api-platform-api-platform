<?php

declare(strict_types=1);

namespace Tests\AppBundle\Bridge\Doctrine\Mapping;

use AppBundle\Bridge\Doctrine\Mapping\NamingStrategy;
use PHPUnit\Framework\TestCase;

class NamingStrategyTest extends TestCase
{
    public function testClassToTableName(): void
    {
        $namingStrategy = new NamingStrategy();

        $this->assertEquals('images', $namingStrategy->classToTableName('Image'));
        $this->assertEquals('galleries', $namingStrategy->classToTableName('Gallery'));
    }

    /**
     * {@inheritdoc}
     */
    public function testJoinKeyColumnName(): void
    {
        $namingStrategy = new NamingStrategy();

        $this->assertEquals('image_id', $namingStrategy->joinKeyColumnName('Image', 'id'));
        $this->assertEquals('gallery_id', $namingStrategy->joinKeyColumnName('Gallery', 'id'));
    }

    /**
     * {@inheritdoc}
     */
    public function testJoinTableName(): void
    {
        $namingStrategy = new NamingStrategy();

        $this->assertEquals('gallery_images', $namingStrategy->joinTableName('Gallery', 'Image', 'images'));
    }
}
