<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace console\controllers;

use common\base\console\Controller;
use common\base\enum\CurrencyCode;
use common\base\enum\CurrencyFormat;
use common\base\helpers\ArrayHelper;
use common\base\helpers\Json;
use common\models\City;
use common\models\Country;
use common\models\Currency;
use common\models\Language;
use common\models\State;

/**
 * Class SetupController
 * @package console\controllers
 */
class SetupController extends Controller
{
    /**
     * initialize system data
     */
    public function actionInit()
    {
        $this->processCurrencyInitialization();
        $this->processLanguageInitialization();
        $this->processCountryStateInitialization();
//        $this->processCityInitialization();
    }

    /**
     * initialize currency
     */
    protected function processCurrencyInitialization()
    {
        $currencies = [
            ['code' => CurrencyCode::HUSTLE_POINT, 'name' => 'Hustle Hero Point', 'shortName' => 'Point', 'symbol' => 'Pts', 'format' => CurrencyFormat::VALUE_SYMBOL],
            ['code' => CurrencyCode::AUSTRALIAN_DOLLAR, 'name' => 'Australian Dollar', 'shortName' => 'Dollar', 'symbol' => 'A$'],
        ];
        $this->info('Total currencies to be loaded: ' . count($currencies));
        $success = 0;

        foreach ($currencies as $index => $currency) {
            $this->progress(sprintf('%d. Creating currency: %s....', ++$index, $currency['name']));
            if ($model = Currency::findOne($currency['code']) !== null) {
                $this->info('Currency exists. skipped!');
                continue;
            }

            if (!isset($currency['shortName'])) {
                $currency['shortName'] = $currency['name'];
            }

            $model = new Currency($currency);
            $valid = $model->save();
            if ($valid) {
                $success++;
                $this->success('Successful!');
            } else {
                $this->error('Failed!');
            }
        }

        $this->info('Total currencies loaded: ' . $success);
    }

    /**
     * initialize language
     */
    protected function processLanguageInitialization()
    {
        $languages = [
            ['code' => 'en', 'name' => 'English', 'shortName' => 'EN'],
            ['code' => 'zh', 'name' => 'Chinese', 'shortName' => 'ZH'],
        ];
        $this->info('Total languages to be loaded: ' . count($languages));
        $success = 0;

        foreach ($languages as $index => $language) {
            $this->progress(sprintf('%d. Creating language: %s....', ++$index, $language['name']));
            if ($model = Language::findOne($language['code']) !== null) {
                $this->info('Language exists. skipped!');
                continue;
            }

            $model = new Language($language);
            $valid = $model->save();
            if ($valid) {
                $success++;
                $this->success('Successful!');
            } else {
                $this->error('Failed!');
            }
        }

        $this->info('Total languages loaded: ' . $success);
    }

    /**
     * Country & states initialization
     */
    protected function processCountryStateInitialization()
    {
        $countries = [
            ['code' => 'AU', 'iso3' => 'AUS', 'currencyCode' => 'AUD', 'numCode' => '036', 'telCode' => '61', 'name' => 'Australia', 'shortName' => 'Australia'],
            ['code' => 'MY', 'iso3' => 'MYS', 'currencyCode' => 'MYR', 'numCode' => '458', 'telCode' => '60', 'name' => 'Malaysia', 'shortName' => 'Malaysia'],
            ['code' => 'SG', 'iso3' => 'SGP', 'currencyCode' => 'SGD', 'numCode' => '702', 'telCode' => '65', 'name' => 'Singapore', 'shortName' => 'Singapore'],
        ];
        $this->info('Total countries to be loaded: ' . count($countries));
        $success = 0;

        foreach ($countries as $index => $country) {
            $this->progress(sprintf('%d. Creating country: %s....', ++$index, $country['name']));
            if (($model = Country::findOne($country['code'])) !== null) {
                $this->info('Country exists. skipped!');
                continue;
            }

            if (!isset($country['shortName'])) {
                $country['shortName'] = $country['name'];
            }

            $model = new Country($country);
            if ($model->save()) {
                $success++;
                $this->success('Success!');
            } else {
                $this->error(Json::encode($model->errors));
                $this->error('Failed!');
            }
        }

        $this->info('Total countries loaded: ' . $success);

        $states = $this->getStates();
        $this->info('Total states to be loaded: ' . count($states));
        $success = 0;

        foreach ($states as $index => $state) {
            $this->progress(sprintf('%d. Creating state: %s....', ++$index, $state['name']));
            if (($model = State::findOne($state['code'])) !== null) {
                $this->info('State exists. skipped!');
                continue;
            }

            if (!isset($state['shortName'])) {
                $state['shortName'] = $state['name'];
            }

            $model = new State($state);
            if ($model->save()) {
                $success++;
                $this->success('Success!');
            } else {
                $this->error('Failed!');
            }
        }

        $this->info('Total states loaded: ' . $success);
    }

    protected function getStates()
    {
        return array_merge(
            $this->getMalaysiaStates(),
            $this->getSingaporeStates(),
            $this->getAustraliaStates(),
        );
    }

    protected function getMalaysiaStates()
    {
        return [
            ['code' => 'MY-01', 'name' => 'Johor', 'shortName' => 'Johor', 'countryCode' => 'MY'],
            ['code' => 'MY-02', 'name' => 'Kedah', 'shortName' => 'Kedah', 'countryCode' => 'MY'],
            ['code' => 'MY-03', 'name' => 'Kelantan', 'shortName' => 'Kelantan', 'countryCode' => 'MY'],
            ['code' => 'MY-04', 'name' => 'Melaka', 'shortName' => 'Melaka', 'countryCode' => 'MY'],
            ['code' => 'MY-05', 'name' => 'Negeri Sembilan', 'shortName' => 'Negeri Sembilan', 'countryCode' => 'MY'],
            ['code' => 'MY-06', 'name' => 'Pahang', 'shortName' => 'Pahang', 'countryCode' => 'MY'],
            ['code' => 'MY-07', 'name' => 'Pulau Pinang', 'shortName' => 'Pulau Pinang', 'countryCode' => 'MY'],
            ['code' => 'MY-08', 'name' => 'Perak', 'shortName' => 'Perak', 'countryCode' => 'MY'],
            ['code' => 'MY-09', 'name' => 'Perlis', 'shortName' => 'Perlis', 'countryCode' => 'MY'],
            ['code' => 'MY-10', 'name' => 'Selangor', 'shortName' => 'Selangor', 'countryCode' => 'MY'],
            ['code' => 'MY-11', 'name' => 'Terengganu', 'shortName' => 'Terengganu', 'countryCode' => 'MY'],
            ['code' => 'MY-12', 'name' => 'Sabah', 'shortName' => 'Sabah', 'countryCode' => 'MY'],
            ['code' => 'MY-13', 'name' => 'Sarawak', 'shortName' => 'Sarawak', 'countryCode' => 'MY'],
            ['code' => 'MY-14', 'name' => 'Kuala Lumpur', 'shortName' => 'Kuala Lumpur', 'countryCode' => 'MY'],
            ['code' => 'MY-15', 'name' => 'Labuan', 'shortName' => 'Labuan', 'countryCode' => 'MY'],
            ['code' => 'MY-16', 'name' => 'Putrajaya', 'shortName' => 'Putrajaya', 'countryCode' => 'MY'],
        ];
    }

    protected function getSingaporeStates()
    {
        return [
            ['code' => 'SG-01', 'name' => 'Central Singapore', 'shortName' => 'Central Singapore', 'countryCode' => 'SG'],
            ['code' => 'SG-02', 'name' => 'North East', 'shortName' => 'North East', 'countryCode' => 'SG'],
            ['code' => 'SG-03', 'name' => 'North West', 'shortName' => 'North West', 'countryCode' => 'SG'],
            ['code' => 'SG-04', 'name' => 'South East', 'shortName' => 'South East', 'countryCode' => 'SG'],
            ['code' => 'SG-05', 'name' => 'South West', 'shortName' => 'South West', 'countryCode' => 'SG'],
        ];
    }

    protected function getAustraliaStates()
    {
        return [
            ['code' => 'AU-NSW', 'name' => 'New South Wales', 'shortName' => 'New South Wales', 'countryCode' => 'AU'],
            ['code' => 'AU-QLD', 'name' => 'Queensland', 'shortName' => 'Queensland', 'countryCode' => 'AU'],
            ['code' => 'AU-SA', 'name' => 'South Australia', 'shortName' => 'South Australia', 'countryCode' => 'AU'],
            ['code' => 'AU-TAS', 'name' => 'Tasmania', 'shortName' => 'Tasmania', 'countryCode' => 'AU'],
            ['code' => 'AU-VIC', 'name' => 'Victoria', 'shortName' => 'Victoria', 'countryCode' => 'AU'],
            ['code' => 'AU-WA', 'name' => 'Western Australia', 'shortName' => 'Western Australia', 'countryCode' => 'AU'],
            ['code' => 'AU-ACT', 'name' => 'Australian Capital Territory', 'shortName' => 'Australian Capital Territory', 'countryCode' => 'AU'],
            ['code' => 'AU-NT', 'name' => 'Northern Territory', 'shortName' => 'Northern Territory', 'countryCode' => 'AU'],
        ];
    }

    /**
     * City initialization
     */
    protected function processCityInitialization()
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load(\Yii::getAlias('@console/controllers/data/malaysia_city.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        //first row is header
        //0 code
        //1 name
        //2 shortName
        //3 stateCode
        //4 countryCode
        $rows = $sheet->toArray();
        $attributes = ArrayHelper::remove($rows, 0);

        $items = [];
        foreach ($rows as $i => $row) {
            $items[] = array_combine(array_filter($attributes), array_filter($row));
        }

        $this->info('Total cities to be loaded: ' . count($items));
        $success = 0;

        foreach ($items as $i => $item) {
            $this->progress(sprintf('%d. Creating city: %s....', ++$i, $item['name']));

            if (($city = City::find()->name($item['name'])->state($item['stateCode'])->exists())) {
                $this->warning('City already exists. skipped!');
                continue;
            }

            if (($state = State::find()->code($item['stateCode'])->country($item['countryCode'])->one()) === null) {
                $this->warning('State does not exist. skipped!');
                continue;
            }

            if (!isset($item['shortName'])) {
                $item['shortName'] = $item['name'];
            }

            //no code column
            ArrayHelper::remove($item, 'code');

            $model = new City($item);
            if ($model->save()) {
                $success++;
                $this->success('Success!');
            } else {
                $this->error(Json::encode($model->errors));
                $this->error('Failed!');
            }
        }

        $this->info('Total cities loaded: ' . $success);
    }
}
