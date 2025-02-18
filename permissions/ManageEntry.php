<?php

namespace humhub\modules\external_calendar\permissions;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use humhub\libs\BasePermission;

/**
 * ManageEntry Permission
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class ManageEntry extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'external_calendar';

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        User::USERGROUP_SELF,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_USER,
        Space::USERGROUP_GUEST,
        User::USERGROUP_FRIEND,
        User::USERGROUP_GUEST,
        User::USERGROUP_USER,
    ];



    public function getTitle()
    {
        return Yii::t('ExternalCalendarModule.base', 'Manage external entries');
    }

    public function getDescription()
    {
        return Yii::t('ExternalCalendarModule.base', 'Allows the user to edit/delete existing external calendar entries');
    }


}
