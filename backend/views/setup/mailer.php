<?php
/**
 * @@copyright Copyright (c) Hustle Hero
 *
 * @var $setup \backend\setups\MailerSetup
 * @var $this \backend\base\web\View
 */

use common\base\enum\MailerDriver;
use common\base\helpers\Json;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'breadcrumb.system_setting');
$this->params['breadcrumbs'][] = Yii::t('backend', 'system_setting.mailer');

?>

<div class="card card-light h-100">
  <?php $form = ActiveForm::begin(['id' => 'mailer-setup-form', 'options' => ['autocomplete' => 'off']]); ?>
  <div class="card-body">
    <?php if ($setup->hasErrors()) : ?>
      <div class="row">
        <div class="col-xs-12">
          <?= $form->errorSummary($setup) ?>
        </div>
      </div>
    <?php endif ?>

    <section id="app">
      <div class="row">
        <div class="col-sm-12">
          <?= $form->field($setup, 'driver')->dropDownList(MailerDriver::options(), ['prompt' => 'Select One', 'v-model' => 'driver']) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <?= $form->field($setup, 'senderName')->hint('e.g. PropertyGenie')->textInput() ?>
        </div>
        <div class="col-sm-6">
          <?= $form->field($setup, 'senderEmail')->hint('e.g. admin@hustlehero.com.au')->textInput() ?>
        </div>
      </div>

      <?= Html::beginTag('template', ['v-if' => 'isSmtpSelected']) ?>
      <div class="card card-secondary">
        <div class="card-header">
          <h4 class="card-title">SMTP Configuration</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <?= $form->field($setup, 'smtpDomainName')->hint('Fully qualified domain name (leave this field empty if you don\'t know).')->textInput() ?>
              <?= $form->field($setup, 'smtpServer')->hint('IP address or server name (e.g. smtp.arpit.com).')->textInput() ?>
              <?= $form->field($setup, 'smtpUsername')->hint('Leave blank if not applicable.')->textInput() ?>
              <?= $form->field($setup, 'smtpPassword')->hint('Leave blank if not applicable.')->textInput() ?>
              <?= $form->field($setup, 'smtpEncryption')->hint('Use an encrypt protocol.')->dropDownList($setup->getSmtpEncryptionOptions()) ?>
              <?= $form->field($setup, 'smtpPort')->hint('Port number to use.')->textInput() ?>
            </div>
          </div>
        </div>
      </div>


      <?= Html::endTag('template') ?>

      <?= Html::beginTag('template', ['v-if' => 'isMailgunSelected']) ?>
      <div class="card card-secondary">
        <div class="card-header">
          <h4 class="card-title">Mailgun Configuration</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <?= $form->field($setup, 'mailgunDomain')->hint('The domain for your mailgun account. e.g. mg.hustlehero.com.au')->textInput() ?>
              <?= $form->field($setup, 'mailgunKey')->hint('The key provided by mailgun portal')->textInput() ?>
            </div>
          </div>
        </div>
      </div>
      <?= Html::endTag('template') ?>

      <?= Html::beginTag('template', ['v-if' => 'isSparkpostSelected']) ?>
      <div class="card card-secondary">
        <div class="card-header">
          <h4 class="card-title">Sparkpost Configuration</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <?= $form->field($setup, 'sparkpostBaseUrl')->hint('The baseUrl for your sparkpost account. e.g. https://api.sparkpost.com/api/v1')->textInput() ?>
              <?= $form->field($setup, 'sparkpostApiKey')->hint('The API key provided by sparkpost portal')->textInput() ?>
            </div>
          </div>
        </div>
      </div>
      <?= Html::endTag('template') ?>
    </section>

      <div class="row">
          <div class="col-sm-12 text-right">
              <button type="submit" class="btn btn-danger btn-flat"><?= Yii::t('backend', 'form.button.submit') ?></button>
          </div>
      </div>
  </div>
  <?php ActiveForm::end() ?>
</div>



<?php
$data = Json::encode($setup->attributes);
$js = <<<EOL
var app = new Vue({
  el: '#app',
  data: {$data},
  computed: {
    isSmtpSelected: function () {
      return this.driver === 'smtp';
    },
    isMailgunSelected: function () {
      return this.driver === 'mailgun';
    },
    isSparkpostSelected: function () {
      return this.driver === 'sparkpost';
    },
  }
});
EOL;
$this->registerJs($js);

?>



