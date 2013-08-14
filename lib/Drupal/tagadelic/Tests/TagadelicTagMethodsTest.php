<?php

/**
 * @file
 * Contains Drupal\tagadelic\Tests\TagadelicTagMethodsTest.
 */

namespace Drupal\tagadelic\Tests;

/**
 * Class TagadelicTagMethodsTest
 *
 * Test-group for testing the output-method __ToString from TagadelicTagTest.
 *   This is a functional group, with lots of duplication, hence it is extracted
 *   to its own Test.
 */
class TagadelicTagMethodsTest extends TagadelicTagTest {

  /**
   * @covers TagadelicTag::getId
   */
  public function testGet_id() {
    $this->assertSame(42, $this->object->getId());
  }

  /**
   * @covers TagadelicTag::getName
   */
  public function testGet_name() {
    $this->assertSame("blackbeard", $this->object->getName());
  }

  /**
   * @covers TagadelicTag::getDescription
   */
  public function testGet_description() {
    $this->object->setDescription("Foo Bar");
    $this->assertSame("Foo Bar", $this->object->getDescription());
  }

  /**
   * @covers TagadelicTag::getWeight
   */
  public function testGet_weight() {
    $this->object->setWeight(123);
    $this->assertSame(123, $this->object->getWeight());
  }

  /**
   * @covers TagadelicTag::getWeight
   */
  public function testGet_count() {
    $this->assertSame(2, $this->object->getCount());
  }

  /**
   * @covers TagadelicTag::setWeight
   */
  public function testSet_weight() {
    $this->object->setWeight(123);
    $this->assertAttributeSame(123, "weight", $this->object);
  }

  /**
   * @covers TagadelicTag::setDrupalWrapper
   */
  public function testSet_drupal() {
    $drupal = $this->getMock("TagaDelicDrupalWrapper");
    $this->object->setDrupalWrapper($drupal);
    $this->assertAttributeSame($drupal, "drupal", $this->object);
  }

  /**
   * @covers TagadelicTag::drupal
   */
  public function testDrupal() {
    $drupal = $this->getMock("TagaDelicDrupalWrapper");
    $this->object->setDrupalWrapper($drupal);
    $this->assertSame($this->object->drupal(), $drupal);
  }

  /**
   * @covers TagadelicTag::drupal
   */
  public function testDrupalInstatiatesNewWrapper() {
    $this->object->setDrupalWrapper(NULL);
    $this->assertInstanceOf("TagaDelicDrupalWrapper", $this->object->drupal());
  }

  /**
   * @covers TagadelicTag::setDescription
   */
  public function testSet_description() {
    $this->object->setDescription("Foo Bar");
    $this->assertAttributeSame("Foo Bar", "description", $this->object);
  }

  /**
   * @covers TagadelicTag::setLink
   */
  public function testSet_link() {
    $this->object->setLink("tag/blackbeard");
    $this->assertAttributeSame("tag/blackbeard", "link", $this->object);
  }

  /**
   * @covers TagadelicTag::forceDirty
   */
  public function testForce_dirty() {
    $this->object->forceDirty();
    $this->assertAttributeSame(TRUE, "dirty", $this->object);
  }

  /**
   * @covers TagadelicTag::forceClean
   */
  public function testForce_clean() {
    $this->object->forceClean();
    $this->assertAttributeSame(FALSE, "dirty", $this->object);
  }

  /**
   * @covers TagadelicTag::clean()
   */
  public function testCleansWhenDirty() {
    $drupal = $this->getMock("TagaDelicDrupalWrapper");
    $drupal->expects($this->exactly(2))->method("check_plain");

    $this->object->setDrupalWrapper($drupal);
    $this->object->forceDirty();

    $this->object->getName();
    $this->object->getDescription();
  }

  /**
   * @covers TagadelicTag::clean()
   */
  public function testSkipsCleanWhenClean() {
    $drupal = $this->getMock("TagaDelicDrupalWrapper");
    $drupal->expects($this->never())->method("check_plain");

    $this->object->setDrupalWrapper($drupal);
    $this->object->forceClean();

    $this->object->getName();
    $this->object->getDescription();
  }
  /**
   * @covers TagadelicTag::distributed
   */
  public function testDistributed() {
    $this->assertSame(log(2), $this->object->distributed());
  }

  /**
   * @covers TagadelicTag::distributed
   */
  public function testDistributed_NotInfinite() {

    $this->object = new TagadelicTag(24, "redhair", 0);

    $this->assertFalse((is_infinite($this->object->distributed())));
  }

}
