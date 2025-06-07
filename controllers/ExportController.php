<?php

namespace humhub\modules\external_calendar\controllers;

use humhub\components\access\ControllerAccess;
use humhub\modules\external_calendar\Module;
use Yii;
use yii\web\HttpException;
use humhub\modules\external_calendar\integration\calendar\CalendarExportService;
use humhub\modules\external_calendar\models\CalendarExport;
use humhub\components\Controller;
use humhub\modules\external_calendar\models\CalendarExportSpaces;
use humhub\modules\space\widgets\Chooser;

class ExportController extends Controller
{
    public $requireContainer = false;

    /**
     * @inheritDoc
     */
    public $access = ControllerAccess::class;

    /**
     * @var CalendarExportService
     */
    private $exportService;

    public function init()
    {
        parent::init();
        $this->exportService = Yii::createObject(CalendarExportService::class);
    }

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            ['login' => ['edit', 'search-space']],
        ];
    }

    /**
     * @param $token
     * @return \yii\web\Response
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionExport($token, $from = null, $to = null)
    {
        $from = ($from) ? (new \DateTime())->setTimestamp($from) : null;
        $to = ($to) ? (new \DateTime())->setTimestamp($to) : null;
        $ics = $this->exportService->createIcsByExportToken($token, $from, $to);

        /** @var Module $module */
        $module = Yii::$app->getModule('external_calendar');
        return Yii::$app->response->sendContentAsFile($ics, $module->exportFileName, ['mimeType' => $module->exportFileMime]);
    }

    public function actionEdit()
    {
        return $this->renderAjax('config');
    }

    public function actionDelete($id)
    {
        $this->forcePostRequest();

        $model = CalendarExport::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if (!$model) {
            throw new HttpException(404);
        }

        $model->delete();

        return $this->renderAjax('config');
    }

}
