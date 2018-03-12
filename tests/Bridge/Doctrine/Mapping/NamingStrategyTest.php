<?php

declare(strict_types=1);

namespace App\Tests\Bridge\Doctrine\Mapping;

use App\Bridge\Doctrine\Mapping\NamingStrategy;
use PHPUnit\Framework\TestCase;

class NamingStrategyTest extends TestCase
{
    public function testClassToTableName(): void
    {
        $namingStrategy = new NamingStrategy();

        $this->assertEquals('images', $namingStrategy->classToTableName('Image'));
        $this->assertEquals('galleries', $namingStrategy->classToTableName('Gallery'));
    }

    public function testJoinKeyColumnName(): void
    {
        $namingStrategy = new NamingStrategy();

        $this->assertEquals('image_id', $namingStrategy->joinKeyColumnName('Image', 'id'));
        $this->assertEquals('gallery_id', $namingStrategy->joinKeyColumnName('Gallery', 'id'));
    }

    public function testJoinTableName(): void
    {
        $namingStrategy = new NamingStrategy();

        $this->assertEquals('gallery_images', $namingStrategy->joinTableName('Gallery', 'Image', 'images'));
    }
}
