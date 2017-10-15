<?php

/**
 * @file
 * Contains \Drupal\kbs_restful_controller\Controller\KBSRestfulAPIController
 */

namespace Drupal\kbs_restful_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;
// Load the image style configuration entity.
use Drupal\image\Entity\ImageStyle;


header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");

/**
 * Controller routines for kbs_restful_api routes.
 */
class KBSRestfulAPIController extends ControllerBase {

    /**
     * Callback for `my-api/get.json` API method.
     */
    public function get_projects(Request $request) {

        $slug = $_GET['slug'];
        if (!empty($slug)) {
            $alias = '/' . $slug;
            $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
            if (preg_match('/node\/(\d+)/', $path, $matches)) {
                $nid = $matches[1];
            }
            $node = Node::load($nid);
            if (!empty($node)) {
                $response["projects"] = $this->load_node_values($node);
            }
        } else {
            $values = [
                'type' => 'project'
            ];
            $nodes = \Drupal::entityTypeManager()->getListBuilder('node')->getStorage()->loadByProperties($values);
            $projects = array();
            foreach ($nodes as $node) {
                $project = $this->load_node_values($node);
                $projects[] = $project;
            }
            $response["projects"] = $projects;
        }
        return new JsonResponse($response);
    }

    /**
     * Callback for `my-api/get.json` API method.
     */
    public function get_posts(Request $request) {
        $slug = $_GET['slug'];
        if (!empty($slug)) {
            $alias = '/' . $slug;
            $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
            if (preg_match('/node\/(\d+)/', $path, $matches)) {
                $nid = $matches[1];
            }
            $node = Node::load($nid);
            if (!empty($node)) {
                $response["posts"] = $this->load_node_values($node);
            }
        } else {
            $values = [
                'type' => 'post'
            ];
            $nodes = \Drupal::entityTypeManager()->getListBuilder('node')->getStorage()->loadByProperties($values);
            $posts = array();
            foreach ($nodes as $node) {
                $post = $this->load_node_values($node);
                $posts[] = $post;
            }
            $response["posts"] = $posts;
        }
        return new JsonResponse($response);
    }

    /**
     * 
     */
    function load_node_values($node) {
        $nid = $node->nid->value;
        $result['id'] = $nid;
        $result['title'] = $node->title->value;
        $result['slug'] = mb_substr(\Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $nid), 1);
        $result['category'] = $node->field_category->value;
        $attributes = array();
        $style = ImageStyle::load('max_100x100');
        $attributes['image']['src'] = file_create_url($node->field_image->entity->getFileUri());
        $attributes['image']['alt'] = $node->field_image->alt;
      //  $attributes['image']['small']['src'] = $style->buildUri($attributes['image']['src']);
        $attributes['description']['full'] = $node->body->value;
        $attributes['description']['summary'] = $node->body->summary;
        $result['attributes'] = $attributes;
        return $result;
    }

}
