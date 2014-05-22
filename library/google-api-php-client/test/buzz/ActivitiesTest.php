<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

class ActivitiesTest extends apiBuzzTest {

  public function testGetPublicStream() {
    global $apiConfig;
    $activities = $this->buzz->activities->listActivities($apiConfig['oauth_test_user'], '@self');
    $this->evaluateActivitiesStream($activities);
  }

  public function testCountActivities() {
    $optParams = array('url' => array("http://google.com", "http://code.google.com"));
    $count = $this->buzz->activities->count($optParams);
    $this->assertTrue(count($count['counts']) == 2);
  }

  /**
   * @depends testGetPublicStream
   */
  public function testInsertUpdateAndDeleteActivity() {
    global $apiConfig;
    
    $object = new ActivityObject();
    $object->type = 'note';
    $object->content = 'Running Google API PHP Client unit-tests';
    
    $activity = new Activity();
    $activity->object = $object;

    // create an activity
    $activity = $this->buzz->activities->insert($apiConfig['oauth_test_user'], $activity);

    // see if what we got back is actually an  activity
    $this->assertArrayHasKey('kind', $activity);
    $this->assertEquals('buzz#activity', $activity['kind']);

    // Is the auto-generated title what is expected?
    $this->assertArrayHasKey('title', $activity);
    $this->assertEquals('Running Google API PHP Client unit-tests', $activity['title']);

    // basic fields
    $this->assertArrayHasKey('published', $activity);
    $this->assertArrayHasKey('updated', $activity);
    $this->assertArrayHasKey('id', $activity);
    $this->assertArrayHasKey('links', $activity);

    // evaluate the resulting object
    $this->assertArrayHasKey('object', $activity);
    $this->assertArrayHasKey('type', $activity['object']);
    $this->assertEquals('note', $activity['object']['type']);

    // and check if the content is what we put in
    $this->assertArrayHasKey('content', $activity['object']);
    $this->assertEquals('Running Google API PHP Client unit-tests', $activity['object']['content']);
    $this->assertArrayHasKey('originalContent', $activity['object']);
    $this->assertEquals('Running Google API PHP Client unit-tests', $activity['object']['content']);

    // see if the post shows up in the @me/@self activities
    $foundActivity = false;
    $activities = $this->buzz->activities->listActivities('@me', '@self');
    foreach ($activities['items'] as $item) {
      if (isset($item['object']['content']) && $item['object']['content'] == 'Running Google API PHP Client unit-tests') {
        $foundActivity = true;
        break;
      }
    }
    $this->assertTrue($foundActivity);

    // test single activity retrieving
    $newActivity = $this->buzz->activities->get($apiConfig['oauth_test_user'], $activity['id']);

    // when you are the author of a buzz post, it returns a 'originalContent' field with the non-link--and-names-expanded, original content (used for editing)
    $this->assertArrayHasKey('originalContent', $newActivity['object']);
    $this->assertEquals('Running Google API PHP Client unit-tests', $newActivity['object']['originalContent']);
    $this->assertEquals($newActivity['id'], $activity['id']);
    $this->assertEquals($newActivity['published'], $activity['published']);
    $this->assertEquals($newActivity['updated'], $activity['updated']);
    $this->assertEquals($newActivity['links'], $activity['links']);
    $this->assertEquals($newActivity['object'], $activity['object']);

    // test updating the activity
    $activity['object']['content'] = 'Google API PHP Client unit-test with updated content';

    $updated = new Activity();
    $updated->actor = $activity['actor'];
    $updated->object = $activity['object'];
    $newActivity = $this->buzz->activities->update($apiConfig['oauth_test_user'], '@self', $activity['id'], $updated);

    // see if the ID & published are the same, and published and the object are updated
    $this->assertEquals($newActivity['id'], $activity['id']);
    $this->assertEquals($newActivity['object']['content'], $activity['object']['content']);
    $this->assertEquals($newActivity['published'], $activity['published']);
    $this->assertNotEquals($newActivity['links'], $activity['links']);
    $this->assertNotEquals($newActivity['updated'], $activity['updated']);

    // test deleteActivities
    $this->buzz->activities->delete('@me', '@self', $activity['id']);
  }

  public function testKeywordSearch() {
    $activities = $this->buzz->activities->search(
      array(
           'max-results' => 10,
           'q' => 'google'
      ));
    $this->evaluateActivitiesStream($activities);
  }

  /**
   * @depends testKeywordSearch
   */
  public function testLocationSearch() {
    // a 5000 meter radius around Mountain View, CA, USA
    $opt_params = array(
      'lat' => '122.0843',
      'lon' => '37.4220',
      'radius' => '5000'
    );
    $activities = $this->buzz->activities->search($opt_params);
    $this->evaluateActivitiesStream($activities);
  }

  /**
   * used by both the authenticated and unauthenticated stream tests
   * @param array $activities
   */
  private function evaluateActivitiesStream($activities) {

    // valid response?
    $this->assertTrue(is_array($activities));

    // basic top level fields
    $this->assertArrayHasKey('kind', $activities);
    $this->assertArrayHasKey('title', $activities);
    $this->assertArrayHasKey('updated', $activities);
    $this->assertArrayHasKey('id', $activities);
    $this->assertArrayHasKey('links', $activities);
    foreach ($activities['links'] as $key => $val) {
      foreach ($val as $link) {
        $this->assertArrayHasKey('href', $link, print_r($link, true));
      }
    }
    $this->assertArrayHasKey('items', $activities);

    // basic activity stream
    $this->assertTrue(count($activities['items']) > 0);
    $this->assertArrayHasKey('kind', $activities['items'][0]);
    $this->assertArrayHasKey('title', $activities['items'][0]);
    $this->assertArrayHasKey('published', $activities['items'][0]);
    $this->assertArrayHasKey('updated', $activities['items'][0]);
    $this->assertArrayHasKey('id', $activities['items'][0]);
    $this->assertArrayHasKey('links', $activities['items'][0]);
    $this->assertArrayHasKey('actor', $activities['items'][0]);
    $this->assertArrayHasKey('object', $activities['items'][0]);
    $this->assertArrayHasKey('verbs', $activities['items'][0]);
    $this->assertArrayHasKey('source', $activities['items'][0]);
    $this->assertArrayHasKey('visibility', $activities['items'][0]);

    // links
    $this->assertArrayHasKey('liked', $activities['items'][0]['links']);
    $this->assertArrayHasKey('alternate', $activities['items'][0]['links']);
    $this->assertArrayHasKey('replies', $activities['items'][0]['links']);
    $this->assertArrayHasKey('self', $activities['items'][0]['links']);
    foreach ($activities['items'][0]['links'] as $key => $val) {
      foreach ($val as $link) {
        $this->assertArrayHasKey('href', $link, print_r($link, true));
        $this->assertArrayHasKey('type', $link, print_r($link, true));
      }
    }

    // actor
    $this->assertArrayHasKey('id', $activities['items'][0]['actor']);
    $this->assertArrayHasKey('name', $activities['items'][0]['actor']);
    $this->assertArrayHasKey('profileUrl', $activities['items'][0]['actor']);
    $this->assertArrayHasKey('thumbnailUrl', $activities['items'][0]['actor']);

    // object
    $this->assertArrayHasKey('type', $activities['items'][0]['object']);
  }

}
