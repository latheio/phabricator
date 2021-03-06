<?php

final class PhabricatorProjectTransaction
  extends PhabricatorApplicationTransaction {

  const TYPE_NAME       = 'project:name';
  const TYPE_SLUGS      = 'project:slugs';
  const TYPE_STATUS     = 'project:status';
  const TYPE_IMAGE      = 'project:image';
  const TYPE_ICON       = 'project:icon';
  const TYPE_COLOR      = 'project:color';
  const TYPE_LOCKED     = 'project:locked';

  // NOTE: This is deprecated, members are just a normal edge now.
  const TYPE_MEMBERS    = 'project:members';

  public function getApplicationName() {
    return 'project';
  }

  public function getApplicationTransactionType() {
    return PhabricatorProjectProjectPHIDType::TYPECONST;
  }

  public function getRequiredHandlePHIDs() {
    $old = $this->getOldValue();
    $new = $this->getNewValue();

    $req_phids = array();
    switch ($this->getTransactionType()) {
      case PhabricatorProjectTransaction::TYPE_MEMBERS:
        $add = array_diff($new, $old);
        $rem = array_diff($old, $new);
        $req_phids = array_merge($add, $rem);
        break;
      case PhabricatorProjectTransaction::TYPE_IMAGE:
        $req_phids[] = $old;
        $req_phids[] = $new;
        break;
    }

    return array_merge($req_phids, parent::getRequiredHandlePHIDs());
  }

  public function getColor() {

    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorProjectTransaction::TYPE_STATUS:
        if ($old == 0) {
          return 'red';
        } else {
          return 'green';
        }
      }
    return parent::getColor();
  }

  public function getIcon() {

    $old = $this->getOldValue();
    $new = $this->getNewValue();

    switch ($this->getTransactionType()) {
      case PhabricatorProjectTransaction::TYPE_STATUS:
        if ($old == 0) {
          return 'fa-ban';
        } else {
          return 'fa-check';
        }
      case PhabricatorProjectTransaction::TYPE_LOCKED:
        if ($new) {
          return 'fa-lock';
        } else {
          return 'fa-unlock';
        }
      case PhabricatorProjectTransaction::TYPE_ICON:
        return $new;
      case PhabricatorProjectTransaction::TYPE_IMAGE:
        return 'fa-photo';
      case PhabricatorProjectTransaction::TYPE_MEMBERS:
        return 'fa-user';
      case PhabricatorProjectTransaction::TYPE_SLUGS:
        return 'fa-tag';
    }
    return parent::getIcon();
  }

  public function getTitle() {
    $old = $this->getOldValue();
    $new = $this->getNewValue();
    $author_handle = $this->renderHandleLink($this->getAuthorPHID());

    switch ($this->getTransactionType()) {
      case PhabricatorProjectTransaction::TYPE_NAME:
        if ($old === null) {
          return pht(
            '%s created this project.',
            $author_handle);
        } else {
          return pht(
            '%s renamed this project from "%s" to "%s".',
            $author_handle,
            $old,
            $new);
        }
      case PhabricatorProjectTransaction::TYPE_STATUS:
        if ($old == 0) {
          return pht(
            '%s archived this project.',
            $author_handle);
        } else {
          return pht(
            '%s activated this project.',
            $author_handle);
        }
      case PhabricatorProjectTransaction::TYPE_IMAGE:
        // TODO: Some day, it would be nice to show the images.
        if (!$old) {
          return pht(
            '%s set this project\'s image to %s.',
            $author_handle,
            $this->renderHandleLink($new));
        } else if (!$new) {
          return pht(
            '%s removed this project\'s image.',
            $author_handle);
        } else {
          return pht(
            '%s updated this project\'s image from %s to %s.',
            $author_handle,
            $this->renderHandleLink($old),
            $this->renderHandleLink($new));
        }

      case PhabricatorProjectTransaction::TYPE_ICON:
        return pht(
          '%s set this project\'s icon to %s.',
          $author_handle,
          PhabricatorProjectIcon::getLabel($new));

      case PhabricatorProjectTransaction::TYPE_COLOR:
        return pht(
          '%s set this project\'s color to %s.',
          $author_handle,
          PHUITagView::getShadeName($new));

      case PhabricatorProjectTransaction::TYPE_LOCKED:
        if ($new) {
          return pht(
            '%s locked this project\'s membership.',
            $author_handle);
        } else {
          return pht(
            '%s unlocked this project\'s membership.',
            $author_handle);
        }

      case PhabricatorProjectTransaction::TYPE_SLUGS:
        $add = array_diff($new, $old);
        $rem = array_diff($old, $new);

        if ($add && $rem) {
          return pht(
            '%s changed project hashtag(s), added %d: %s; removed %d: %s.',
            $author_handle,
            count($add),
            $this->renderSlugList($add),
            count($rem),
            $this->renderSlugList($rem));
        } else if ($add) {
          return pht(
            '%s added %d project hashtag(s): %s.',
            $author_handle,
            count($add),
            $this->renderSlugList($add));
        } else if ($rem) {
            return pht(
              '%s removed %d project hashtag(s): %s.',
              $author_handle,
              count($rem),
              $this->renderSlugList($rem));
        }

      case PhabricatorProjectTransaction::TYPE_MEMBERS:
        $add = array_diff($new, $old);
        $rem = array_diff($old, $new);

        if ($add && $rem) {
          return pht(
            '%s changed project member(s), added %d: %s; removed %d: %s.',
            $author_handle,
            count($add),
            $this->renderHandleList($add),
            count($rem),
            $this->renderHandleList($rem));
        } else if ($add) {
          if (count($add) == 1 && (head($add) == $this->getAuthorPHID())) {
            return pht(
              '%s joined this project.',
              $author_handle);
          } else {
            return pht(
              '%s added %d project member(s): %s.',
              $author_handle,
              count($add),
              $this->renderHandleList($add));
          }
        } else if ($rem) {
          if (count($rem) == 1 && (head($rem) == $this->getAuthorPHID())) {
            return pht(
              '%s left this project.',
              $author_handle);
          } else {
            return pht(
              '%s removed %d project member(s): %s.',
              $author_handle,
              count($rem),
              $this->renderHandleList($rem));
          }
        }
    }

    return parent::getTitle();
  }

  private function renderSlugList($slugs) {
    return implode(', ', $slugs);
  }

}
