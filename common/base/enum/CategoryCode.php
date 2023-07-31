<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\enum;

use common\base\helpers\ArrayHelper;
use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Class CategoryCode
 * @package common\base\enum
 */
class CategoryCode extends BaseEnum
{
    // System
    const UNIT_TYPE = 'unit-type';

    // Bungalow / Villa
    const BUNGALOW_HOUSE = 'bungalow-house';
    const LINK_BUNGALOW = 'link-bungalow';
    const ZERO_LOT_BUNGALOW = 'zero-lot-bungalow';
    const TWIN_VILLAS = 'twin-villas';
    const TWIN_COURTYARD_VILLA = 'twin-courtyard-villa';

    // Apartment / Condo / Service Residence
    const CONDOMINIUM = 'condominium';
    const APARTMENT = 'apartment';
    const FLAT = 'flat';
    const PENTHOUSE = 'penthouse';
    const SERVICE_RESIDENCE = 'service-residence';
    const STUDIO = 'studio';
    const DUPLEX = 'duplex';
    const TOWNHOUSE_CONDOMINIUM = 'townhouse-condominium';

    // Semi-Detached House
    const SEMI_DETACHED_HOUSE = 'semi-detached';
    const CLUSTER_HOUSE = 'cluster';

    // Terrance / Link House
    const TERRACED_HOUSE = 'terraced-house';
    const TOWNHOUSE = 'townhouse';
    const TERRACED_HOUSE_1_S = 'terraced-1s';
    const TERRACED_HOUSE_1_5_S = 'terraced-1.5s';
    const TERRACED_HOUSE_2_S = 'terraced-2s';
    const TERRACED_HOUSE_2_5_S = 'terraced-2.5s';
    const TERRACED_HOUSE_3_S = 'terraced-3s';
    const TERRACED_HOUSE_3_5_S = 'terraced-3.5s';

    // Land
    const RESIDENT_LAND = 'resident-land';
    const AGRICULTURAL_LAND = 'agricultural-land';

    // commercial
    const SHOP = 'shop';
    const SHOP_OFFICE = 'shop-office';
    const OFFICE = 'office';
    const COMMERCIAL_LAND = 'commercial-land';
    const RETAIL_SPACE = 'retail-space';
    const RETAIL_OFFICE = 'retail-office';
    const SOHO = 'soho';

    // Industrial
    const FACTORY = 'factory';
    const WAREHOUSE = 'warehouse';
    const INDUSTRIAL_LAND = 'industrial-land';

    // Other
    const HOTEL_RESORT = 'hotel-resort';
    const COMMERCIAL_OTHERS = 'commercial-others';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::UNIT_TYPE => Yii::t('enum', 'category_code.unit-type'),
            self::BUNGALOW_HOUSE => Yii::t('enum', 'category_code.bungalow-house'),
            self::LINK_BUNGALOW => Yii::t('enum', 'category_code.link-bungalow'),
            self::ZERO_LOT_BUNGALOW => Yii::t('enum', 'category_code.zero-lot-bungalow'),
            self::TWIN_VILLAS => Yii::t('enum', 'category_code.twin-villas'),
            self::TWIN_COURTYARD_VILLA => Yii::t('enum', 'category_code.twin-courtyard-villa'),
            self::CONDOMINIUM => Yii::t('enum', 'category_code.condominium'),
            self::APARTMENT => Yii::t('enum', 'category_code.apartment'),
            self::FLAT => Yii::t('enum', 'category_code.flat'),
            self::PENTHOUSE => Yii::t('enum', 'category_code.penthouse'),
            self::SERVICE_RESIDENCE => Yii::t('enum', 'category_code.service-residence'),
            self::STUDIO => Yii::t('enum', 'category_code.studio'),
            self::DUPLEX => Yii::t('enum', 'category_code.duplex'),
            self::TOWNHOUSE_CONDOMINIUM => Yii::t('enum', 'category_code.townhouse-condominium'),
            self::SEMI_DETACHED_HOUSE => Yii::t('enum', 'category_code.semi-detached'),
            self::CLUSTER_HOUSE => Yii::t('enum', 'category_code.cluster'),
            self::TERRACED_HOUSE => Yii::t('enum', 'category_code.terraced-house'),
            self::TOWNHOUSE => Yii::t('enum', 'category_code.townhouse'),
            self::TERRACED_HOUSE_1_S => Yii::t('enum', 'category_code.terraced-1s'),
            self::TERRACED_HOUSE_1_5_S => Yii::t('enum', 'category_code.terraced-1.5s'),
            self::TERRACED_HOUSE_2_S => Yii::t('enum', 'category_code.terraced-2s'),
            self::TERRACED_HOUSE_2_5_S => Yii::t('enum', 'category_code.terraced-2.5s'),
            self::TERRACED_HOUSE_3_S => Yii::t('enum', 'category_code.terraced-3s'),
            self::TERRACED_HOUSE_3_5_S => Yii::t('enum', 'category_code.terraced-3.5s'),
            self::RESIDENT_LAND => Yii::t('enum', 'category_code.resident-land'),
            self::AGRICULTURAL_LAND => Yii::t('enum', 'category_code.agricultural-land'),
            self::SHOP => Yii::t('enum', 'category_code.shop'),
            self::SHOP_OFFICE => Yii::t('enum', 'category_code.shop-office'),
            self::OFFICE => Yii::t('enum', 'category_code.office'),
            self::COMMERCIAL_LAND => Yii::t('enum', 'category_code.commercial-land'),
            self::RETAIL_SPACE => Yii::t('enum', 'category_code.retail-space'),
            self::RETAIL_OFFICE => Yii::t('enum', 'category_code.retail-office'),
            self::SOHO => Yii::t('enum', 'category_code.soho'),
            self::FACTORY => Yii::t('enum', 'category_code.factory'),
            self::WAREHOUSE => Yii::t('enum', 'category_code.warehouse'),
            self::INDUSTRIAL_LAND => Yii::t('enum', 'category_code.industrial-land'),
            self::HOTEL_RESORT => Yii::t('enum', 'category_code.hotel-resort'),
            self::COMMERCIAL_OTHERS => Yii::t('enum', 'category_code.commercial-others'),
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public static function resolveListingType($code)
    {
        switch ($code) {
            case self::AGRICULTURAL_LAND:
            case self::SHOP:
            case self::SHOP_OFFICE:
            case self::OFFICE:
            case self::COMMERCIAL_LAND:
            case self::RETAIL_SPACE:
            case self::RETAIL_OFFICE:
            case self::SOHO:
            case self::FACTORY:
            case self::WAREHOUSE:
            case self::INDUSTRIAL_LAND:
            case self::HOTEL_RESORT:
            case self::COMMERCIAL_OTHERS:
                $type = ListingType::COMMERCIAL;
                break;
            default:
                $type = ListingType::RESIDENTIAL;
        }

        return $type;
    }

    /**
     * @return array
     */
    public static function toArray()
    {
        $items = [];
        foreach (static::getTypeItems() as $type => $groupItems) {
            $key = strtolower($type);
            $items[$key] = ['items' => []];

            foreach ($groupItems as $group => $options) {
                $code = sprintf('%s-%s', Inflector::slug($type), Inflector::slug($group));
                $subItems = [];
                foreach ($options as $k => $v) {
                    $subItems[] = ['parent' => $code, 'value' => $k, 'name' => $v];
                }

                $items[$key]['items'][] = [
                    ['value' => $code, 'name' => $group, 'items' => $subItems]
                ];
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    public static function filterOptions()
    {
        $options = [];
        foreach (static::getTypeItems() as $type => $groupItems) {
            foreach ($groupItems as $group => $items) {
                foreach ($items as $key => $value) {
                    $options[$key] = $type . ' - ' . $value;
                }
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public static function selectDropdownOptions()
    {
        $options = [];
        foreach (static::getTypeItems() as $type => $groupItems) {
            $options[$type] = [];
            foreach ($groupItems as $group => $items) {
                foreach ($items as $key => $value) {
                    $text = Html::tag('strong', $type, ['class' => 'text-purple']) . ' > ';
                    $text .= Html::tag('span', $group, ['class' => 'text-green']) . ' > ';
                    ArrayHelper::setValue($options, [$type, $key], $text . $value);
                }
            }
        }

        return $options;
    }



    protected static function getTypeItems()
    {
        return [
            'Residential' => [
                'Bungalow / Villa' => [
                    self::BUNGALOW_HOUSE => Yii::t('enum', 'category_code.bungalow-house'),
                    self::LINK_BUNGALOW => Yii::t('enum', 'category_code.link-bungalow'),
                    self::ZERO_LOT_BUNGALOW => Yii::t('enum', 'category_code.zero-lot-bungalow'),
                    self::TWIN_VILLAS => Yii::t('enum', 'category_code.twin-villas'),
                    self::TWIN_COURTYARD_VILLA => Yii::t('enum', 'category_code.twin-courtyard-villa'),
                ],
                'Apartment / Condo / Service Residence' => [
                    self::CONDOMINIUM => Yii::t('enum', 'category_code.condominium'),
                    self::APARTMENT => Yii::t('enum', 'category_code.apartment'),
                    self::FLAT => Yii::t('enum', 'category_code.flat'),
                    self::PENTHOUSE => Yii::t('enum', 'category_code.penthouse'),
                    self::SERVICE_RESIDENCE => Yii::t('enum', 'category_code.service-residence'),
                    self::STUDIO => Yii::t('enum', 'category_code.studio'),
                    self::DUPLEX => Yii::t('enum', 'category_code.duplex'),
                    self::TOWNHOUSE_CONDOMINIUM => Yii::t('enum', 'category_code.townhouse-condominium'),
                ],
                'Semi-Detached House' => [
                    self::SEMI_DETACHED_HOUSE => Yii::t('enum', 'category_code.semi-detached'),
                    self::CLUSTER_HOUSE => Yii::t('enum', 'category_code.cluster'),
                ],
                'Terrace / Link House' => [
                    self::TERRACED_HOUSE => Yii::t('enum', 'category_code.terraced-house'),
                    self::TOWNHOUSE => Yii::t('enum', 'category_code.townhouse'),
                    self::TERRACED_HOUSE_1_S => Yii::t('enum', 'category_code.terraced-1s'),
                    self::TERRACED_HOUSE_1_5_S => Yii::t('enum', 'category_code.terraced-1.5s'),
                    self::TERRACED_HOUSE_2_S => Yii::t('enum', 'category_code.terraced-2s'),
                    self::TERRACED_HOUSE_2_5_S => Yii::t('enum', 'category_code.terraced-2.5s'),
                    self::TERRACED_HOUSE_3_S => Yii::t('enum', 'category_code.terraced-3s'),
                    self::TERRACED_HOUSE_3_5_S => Yii::t('enum', 'category_code.terraced-3.5s'),
                ],
                'Land' => [
                    self::RESIDENT_LAND => Yii::t('enum', 'category_code.resident-land'),
                ],
            ],
            'Commercial' => [
                'Commercial Shop' => [
                    self::SHOP => Yii::t('enum', 'category_code.shop'),
                    self::SHOP_OFFICE => Yii::t('enum', 'category_code.shop-office'),
                    self::OFFICE => Yii::t('enum', 'category_code.office'),
                    self::RETAIL_SPACE => Yii::t('enum', 'category_code.retail-space'),
                    self::RETAIL_OFFICE => Yii::t('enum', 'category_code.retail-office'),
                    self::SOHO => Yii::t('enum', 'category_code.soho'),
                ],
                'Industrial' => [
                    self::FACTORY => Yii::t('enum', 'category_code.factory'),
                    self::WAREHOUSE => Yii::t('enum', 'category_code.warehouse'),
                ],
                'Land' => [
                    self::COMMERCIAL_LAND => Yii::t('enum', 'category_code.commercial-land'),
                    self::INDUSTRIAL_LAND => Yii::t('enum', 'category_code.industrial-land'),
                    self::AGRICULTURAL_LAND => Yii::t('enum', 'category_code.agricultural-land'),
                ],
                'Others' => [
                    self::HOTEL_RESORT => Yii::t('enum', 'category_code.hotel-resort'),
                    self::COMMERCIAL_OTHERS => Yii::t('enum', 'category_code.commercial-others'),
                ],

            ],
        ];
    }

}