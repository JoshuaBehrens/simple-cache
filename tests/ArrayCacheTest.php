<?php

use voku\cache\AdapterArray;
use voku\cache\Cache;
use voku\cache\iAdapter;
use voku\cache\iSerializer;
use voku\cache\SerializerDefault;

/**
 * ArrayCacheTest
 *
 * @internal
 */
final class ArrayCacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var iSerializer
     */
    public $serializer;

    /**
     * @var iAdapter
     */
    public $adapter;

    /**
     * @var Cache
     */
    public $cache;

    protected $backupGlobalsBlacklist = [
        '_SESSION',
    ];

    public function testSetItemOfNull()
    {
        $return = $this->cache->setItem('foo_null', null);

        static::assertTrue($return);

        // -----

        $return = $this->cache->getItem('foo_null');
        static::assertNull($return);
    }

    public function testSetItem()
    {
        $return = $this->cache->setItem('foo', [1, 2, 3, 4]);
        static::assertTrue($return);

        $return = $this->cache->getItem('foo');
        static::assertSame([1, 2, 3, 4], $return);

        // -----

        $ao = new ArrayObject();

        $ao->prop = 'prop data';
        $ao['arr'] = 'array data';

        $return = $this->cache->setItem('ao', $ao);

        static::assertTrue($return);
    }

    public function testGetItem()
    {
        $return = $this->cache->getItem('foo');

        static::assertSame([1, 2, 3, 4], $return);

        // -----

        $return = $this->cache->getItem('ao');

        $ao = new ArrayObject();

        $ao->prop = 'prop data';
        $ao['arr'] = 'array data';

        static::assertSame($ao->prop, $return->prop);
        static::assertSame($ao['arr'], $return['arr']);
    }

    public function testExistsItem()
    {
        $return = $this->cache->existsItem('foo');

        static::assertTrue($return);
    }

    public function testGetCacheIsReady()
    {
        $return = $this->cache->getCacheIsReady();

        static::assertTrue($return);
    }

    public function testSetGetItemWithPrefix()
    {
        $this->cache->setPrefix('bar');
        $prefix = $this->cache->getPrefix();
        static::assertSame('bar', $prefix);

        $return = $this->cache->setItem('foo', [3, 2, 1]);
        static::assertTrue($return);

        $return = $this->cache->getItem('foo');
        static::assertSame([3, 2, 1], $return);
    }

    public function testSetGetCacheWithEndDateTime()
    {
        $expireDate = new DateTime();
        $interval = DateInterval::createFromDateString('+1 seconds');
        $expireDate->add($interval);

        $return = $this->cache->setItemToDate('testSetGetCacheWithEndDateTime', [3, 2, 1], $expireDate);
        static::assertTrue($return);

        $return = $this->cache->getItem('testSetGetCacheWithEndDateTime');
        static::assertSame([3, 2, 1], $return);

        \sleep(2);

        $return = $this->cache->getItem('testSetGetCacheWithEndDateTime');
        static::assertNull($return);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->adapter = new AdapterArray();
        $this->serializer = new SerializerDefault();

        $this->cache = new Cache($this->adapter, $this->serializer, false, true);

        // reset default prefix
        $this->cache->setPrefix('');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}
