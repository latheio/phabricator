<?php

final class PhabricatorMacroApplication extends PhabricatorApplication {

  public function getBaseURI() {
    return '/macro/';
  }

  public function getName() {
    return pht('Macro');
  }

  public function getShortDescription() {
    return pht('Image Macros and Memes');
  }

  public function getIconName() {
    return 'macro';
  }

  public function getTitleGlyph() {
    return "\xE2\x9A\x98";
  }

  public function getApplicationGroup() {
    return self::GROUP_UTILITIES;
  }

  public function getRoutes() {
    return array(
      '/macro/' => array(
        '(query/(?P<key>[^/]+)/)?' => 'PhabricatorMacroListController',
        'create/' => 'PhabricatorMacroEditController',
        'view/(?P<id>[1-9]\d*)/' => 'PhabricatorMacroViewController',
        'comment/(?P<id>[1-9]\d*)/' => 'PhabricatorMacroCommentController',
        'edit/(?P<id>[1-9]\d*)/' => 'PhabricatorMacroEditController',
        'audio/(?P<id>[1-9]\d*)/' => 'PhabricatorMacroAudioController',
        'disable/(?P<id>[1-9]\d*)/' => 'PhabricatorMacroDisableController',
        'meme/' => 'PhabricatorMacroMemeController',
        'meme/create/' => 'PhabricatorMacroMemeDialogController',
      ),
    );
  }

  public function getRemarkupRules() {
    return array(
      new PhabricatorRemarkupRuleIcon(),
    );
  }

  protected function getCustomCapabilities() {
    return array(
      PhabricatorMacroManageCapability::CAPABILITY => array(
        'caption' => pht('Allows creating and editing macros.'),
      ),
    );
  }

}