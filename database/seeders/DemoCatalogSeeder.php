<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCompatibility;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    private function upsertVariantAttributes(ProductVariant $variant, array $attributes): void
    {
        foreach ($attributes as $a) {
            $key = is_array($a) && isset($a['key']) ? (string) $a['key'] : '';
            $value = is_array($a) && array_key_exists('value', $a) ? (string) $a['value'] : '';
            $valueNum = is_array($a) && array_key_exists('value_num', $a) ? $a['value_num'] : null;

            if ($key === '' || $value === '') {
                continue;
            }

            ProductVariantAttribute::updateOrCreate(
                ['product_variant_id' => $variant->id, 'key' => $key, 'value' => $value],
                ['value_num' => $valueNum]
            );
        }
    }

    public function run(): void
    {
        $heatflo = Brand::updateOrCreate(
            ['slug' => 'heatflo'],
            ['name' => 'Heatflo', 'slug' => 'heatflo']
        );

        $victron = Brand::updateOrCreate(
            ['slug' => 'victron-energy'],
            ['name' => 'Victron Energy', 'slug' => 'victron-energy']
        );

        $solis = Brand::updateOrCreate(
            ['slug' => 'solis'],
            ['name' => 'Solis', 'slug' => 'solis']
        );

        $deye = Brand::updateOrCreate(
            ['slug' => 'deye'],
            ['name' => 'Deye', 'slug' => 'deye']
        );

        $growatt = Brand::updateOrCreate(
            ['slug' => 'growatt'],
            ['name' => 'Growatt', 'slug' => 'growatt']
        );

        $must = Brand::updateOrCreate(
            ['slug' => 'must'],
            ['name' => 'Must', 'slug' => 'must']
        );

        $jinko = Brand::updateOrCreate(
            ['slug' => 'jinko'],
            ['name' => 'Jinko Solar', 'slug' => 'jinko']
        );

        $longi = Brand::updateOrCreate(
            ['slug' => 'longi'],
            ['name' => 'LONGi Solar', 'slug' => 'longi']
        );

        $trina = Brand::updateOrCreate(
            ['slug' => 'trina'],
            ['name' => 'Trina Solar', 'slug' => 'trina']
        );

        $solarCategory = Category::updateOrCreate(
            ['slug' => 'solar-water-heaters'],
            ['name' => 'Solar Water Heaters', 'slug' => 'solar-water-heaters']
        );

        $invertersCategory = Category::updateOrCreate(
            ['slug' => 'inverters'],
            ['name' => 'Inverters', 'slug' => 'inverters']
        );

        $panelsCategory = Category::updateOrCreate(
            ['slug' => 'solar-panels'],
            ['name' => 'Solar Panels', 'slug' => 'solar-panels']
        );

        $batteriesCategory = Category::updateOrCreate(
            ['slug' => 'batteries'],
            ['name' => 'Batteries', 'slug' => 'batteries']
        );

        $accessoriesCategory = Category::updateOrCreate(
            ['slug' => 'accessories'],
            ['name' => 'Accessories', 'slug' => 'accessories']
        );

        $product = Product::updateOrCreate(
            ['slug' => '200l-solar-water-heater'],
            [
                'category_id' => $solarCategory->id,
                'brand_id' => $heatflo->id,
                'name' => '200L Solar Water Heater',
                'slug' => '200l-solar-water-heater',
                'short_description' => 'Demo product for local testing.',
                'description' => 'Demo product for local testing.',
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $variant = ProductVariant::updateOrCreate(
            ['product_id' => $product->id, 'title' => 'Standard'],
            [
                'sku' => 'SWH-200L-STD',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $variant->id, 'key' => 'capacity_liters', 'value' => '200'],
            ['value_num' => 200]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $variant->id, 'key' => 'tank_type', 'value' => 'Pressurised'],
            ['value_num' => null]
        );

        $this->upsertVariantAttributes($variant, [
            ['key' => 'collector_type', 'value' => 'Evacuated tubes', 'value_num' => null],
            ['key' => 'working_pressure_bar', 'value' => '6', 'value_num' => 6],
            ['key' => 'tank_material', 'value' => 'Stainless steel', 'value_num' => null],
            ['key' => 'backup_element_kw', 'value' => '3', 'value_num' => 3],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        $product300 = Product::updateOrCreate(
            ['slug' => '300l-solar-water-heater'],
            [
                'category_id' => $solarCategory->id,
                'brand_id' => $heatflo->id,
                'name' => '300L Solar Water Heater',
                'slug' => '300l-solar-water-heater',
                'short_description' => 'High-capacity solar water heater for families and lodges.',
                'description' => 'High-capacity solar water heater for families and lodges.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $variant300 = ProductVariant::updateOrCreate(
            ['product_id' => $product300->id, 'title' => 'Standard'],
            [
                'sku' => 'SWH-300L-STD',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $variant300->id, 'key' => 'capacity_liters', 'value' => '300'],
            ['value_num' => 300]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $variant300->id, 'key' => 'tank_type', 'value' => 'Pressurised'],
            ['value_num' => null]
        );

        $this->upsertVariantAttributes($variant300, [
            ['key' => 'collector_type', 'value' => 'Evacuated tubes', 'value_num' => null],
            ['key' => 'working_pressure_bar', 'value' => '6', 'value_num' => 6],
            ['key' => 'tank_material', 'value' => 'Stainless steel', 'value_num' => null],
            ['key' => 'backup_element_kw', 'value' => '3', 'value_num' => 3],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        $inv5k = Product::updateOrCreate(
            ['slug' => '5kw-hybrid-inverter'],
            [
                'category_id' => $invertersCategory->id,
                'brand_id' => $growatt->id,
                'name' => '5kW Hybrid Inverter',
                'slug' => '5kw-hybrid-inverter',
                'short_description' => 'Hybrid inverter for home backup and solar systems.',
                'description' => 'Hybrid inverter for home backup and solar systems.',
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $inv5kVar = ProductVariant::updateOrCreate(
            ['product_id' => $inv5k->id, 'title' => '48V'],
            [
                'sku' => 'INV-5KW-HYB-48V',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $inv5kVar->id, 'key' => 'power_kw', 'value' => '5'],
            ['value_num' => 5]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $inv5kVar->id, 'key' => 'battery_voltage', 'value' => '48V'],
            ['value_num' => 48]
        );

        $this->upsertVariantAttributes($inv5kVar, [
            ['key' => 'inverter_type', 'value' => 'Hybrid', 'value_num' => null],
            ['key' => 'ac_output_watts', 'value' => '5000', 'value_num' => 5000],
            ['key' => 'output_phase', 'value' => 'Single phase', 'value_num' => null],
            ['key' => 'mppt_count', 'value' => '2', 'value_num' => 2],
            ['key' => 'pv_input_voltage_max_v', 'value' => '450', 'value_num' => 450],
            ['key' => 'max_charge_current_a', 'value' => '100', 'value_num' => 100],
            ['key' => 'efficiency_pct', 'value' => '96', 'value_num' => 96],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        $inv3k = Product::updateOrCreate(
            ['slug' => '3kw-off-grid-inverter'],
            [
                'category_id' => $invertersCategory->id,
                'brand_id' => $must->id,
                'name' => '3kW Off-Grid Inverter',
                'slug' => '3kw-off-grid-inverter',
                'short_description' => 'Reliable off-grid inverter for essential loads.',
                'description' => 'Reliable off-grid inverter for essential loads.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $inv3kVar = ProductVariant::updateOrCreate(
            ['product_id' => $inv3k->id, 'title' => '24V'],
            [
                'sku' => 'INV-3KW-OFF-24V',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $inv3kVar->id, 'key' => 'power_kw', 'value' => '3'],
            ['value_num' => 3]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $inv3kVar->id, 'key' => 'battery_voltage', 'value' => '24V'],
            ['value_num' => 24]
        );

        $this->upsertVariantAttributes($inv3kVar, [
            ['key' => 'inverter_type', 'value' => 'Off-grid', 'value_num' => null],
            ['key' => 'ac_output_watts', 'value' => '3000', 'value_num' => 3000],
            ['key' => 'output_phase', 'value' => 'Single phase', 'value_num' => null],
            ['key' => 'mppt_count', 'value' => '1', 'value_num' => 1],
            ['key' => 'pv_input_voltage_max_v', 'value' => '145', 'value_num' => 145],
            ['key' => 'max_charge_current_a', 'value' => '60', 'value_num' => 60],
            ['key' => 'efficiency_pct', 'value' => '93', 'value_num' => 93],
            ['key' => 'warranty_years', 'value' => '2', 'value_num' => 2],
        ]);

        $inv8k = Product::updateOrCreate(
            ['slug' => '8kw-hybrid-inverter'],
            [
                'category_id' => $invertersCategory->id,
                'brand_id' => $deye->id,
                'name' => '8kW Hybrid Inverter',
                'slug' => '8kw-hybrid-inverter',
                'short_description' => 'Premium hybrid inverter for high-demand homes and small businesses.',
                'description' => 'Premium hybrid inverter for high-demand homes and small businesses.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $inv8kVar = ProductVariant::updateOrCreate(
            ['product_id' => $inv8k->id, 'title' => '48V'],
            [
                'sku' => 'INV-8KW-HYB-48V',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        $this->upsertVariantAttributes($inv8kVar, [
            ['key' => 'power_kw', 'value' => '8', 'value_num' => 8],
            ['key' => 'battery_voltage', 'value' => '48V', 'value_num' => 48],
            ['key' => 'inverter_type', 'value' => 'Hybrid', 'value_num' => null],
            ['key' => 'ac_output_watts', 'value' => '8000', 'value_num' => 8000],
            ['key' => 'output_phase', 'value' => 'Single phase', 'value_num' => null],
            ['key' => 'mppt_count', 'value' => '2', 'value_num' => 2],
            ['key' => 'pv_input_voltage_max_v', 'value' => '500', 'value_num' => 500],
            ['key' => 'max_charge_current_a', 'value' => '150', 'value_num' => 150],
            ['key' => 'efficiency_pct', 'value' => '97', 'value_num' => 97],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        $inv3kVictron = Product::updateOrCreate(
            ['slug' => '3kva-victron-inverter-charger'],
            [
                'category_id' => $invertersCategory->id,
                'brand_id' => $victron->id,
                'name' => '3kVA Inverter/Charger',
                'slug' => '3kva-victron-inverter-charger',
                'short_description' => 'High-end inverter/charger for reliable backup and off-grid systems.',
                'description' => 'High-end inverter/charger for reliable backup and off-grid systems.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $inv3kVictronVar = ProductVariant::updateOrCreate(
            ['product_id' => $inv3kVictron->id, 'title' => '48V'],
            [
                'sku' => 'INV-3KVA-CHG-48V',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        $this->upsertVariantAttributes($inv3kVictronVar, [
            ['key' => 'power_kva', 'value' => '3', 'value_num' => 3],
            ['key' => 'battery_voltage', 'value' => '48V', 'value_num' => 48],
            ['key' => 'inverter_type', 'value' => 'Inverter/Charger', 'value_num' => null],
            ['key' => 'output_phase', 'value' => 'Single phase', 'value_num' => null],
            ['key' => 'charger_current_a', 'value' => '70', 'value_num' => 70],
            ['key' => 'transfer_switch_a', 'value' => '50', 'value_num' => 50],
            ['key' => 'efficiency_pct', 'value' => '94', 'value_num' => 94],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        $panel550 = Product::updateOrCreate(
            ['slug' => '550w-mono-solar-panel'],
            [
                'category_id' => $panelsCategory->id,
                'brand_id' => $jinko->id,
                'name' => '550W Mono Solar Panel',
                'slug' => '550w-mono-solar-panel',
                'short_description' => 'High-efficiency mono panel for rooftop and ground-mount systems.',
                'description' => 'High-efficiency mono panel for rooftop and ground-mount systems.',
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $panel550Var = ProductVariant::updateOrCreate(
            ['product_id' => $panel550->id, 'title' => 'Standard'],
            [
                'sku' => 'PV-550W-MONO',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $panel550Var->id, 'key' => 'power_watts', 'value' => '550'],
            ['value_num' => 550]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $panel550Var->id, 'key' => 'cell_type', 'value' => 'Mono'],
            ['value_num' => null]
        );

        $this->upsertVariantAttributes($panel550Var, [
            ['key' => 'module_efficiency_pct', 'value' => '21.0', 'value_num' => 21.0],
            ['key' => 'vmp_v', 'value' => '41.5', 'value_num' => 41.5],
            ['key' => 'voc_v', 'value' => '49.5', 'value_num' => 49.5],
            ['key' => 'imp_a', 'value' => '13.3', 'value_num' => 13.3],
            ['key' => 'isc_a', 'value' => '14.0', 'value_num' => 14.0],
            ['key' => 'dimensions_mm', 'value' => '2278×1134×35', 'value_num' => null],
            ['key' => 'weight_kg', 'value' => '28', 'value_num' => 28],
            ['key' => 'warranty_years', 'value' => '12', 'value_num' => 12],
        ]);

        $panel410 = Product::updateOrCreate(
            ['slug' => '410w-mono-solar-panel'],
            [
                'category_id' => $panelsCategory->id,
                'brand_id' => $jinko->id,
                'name' => '410W Mono Solar Panel',
                'slug' => '410w-mono-solar-panel',
                'short_description' => 'Compact mono panel ideal for smaller systems.',
                'description' => 'Compact mono panel ideal for smaller systems.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $panel410Var = ProductVariant::updateOrCreate(
            ['product_id' => $panel410->id, 'title' => 'Standard'],
            [
                'sku' => 'PV-410W-MONO',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $panel410Var->id, 'key' => 'power_watts', 'value' => '410'],
            ['value_num' => 410]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $panel410Var->id, 'key' => 'cell_type', 'value' => 'Mono'],
            ['value_num' => null]
        );

        $this->upsertVariantAttributes($panel410Var, [
            ['key' => 'module_efficiency_pct', 'value' => '20.5', 'value_num' => 20.5],
            ['key' => 'vmp_v', 'value' => '31.4', 'value_num' => 31.4],
            ['key' => 'voc_v', 'value' => '37.5', 'value_num' => 37.5],
            ['key' => 'imp_a', 'value' => '13.1', 'value_num' => 13.1],
            ['key' => 'isc_a', 'value' => '13.9', 'value_num' => 13.9],
            ['key' => 'dimensions_mm', 'value' => '1722×1134×30', 'value_num' => null],
            ['key' => 'weight_kg', 'value' => '21.5', 'value_num' => 21.5],
            ['key' => 'warranty_years', 'value' => '12', 'value_num' => 12],
        ]);

        $panel585 = Product::updateOrCreate(
            ['slug' => '585w-mono-solar-panel'],
            [
                'category_id' => $panelsCategory->id,
                'brand_id' => $longi->id,
                'name' => '585W Mono Solar Panel',
                'slug' => '585w-mono-solar-panel',
                'short_description' => 'High-power module for faster energy harvesting in limited roof space.',
                'description' => 'High-power module for faster energy harvesting in limited roof space.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $panel585Var = ProductVariant::updateOrCreate(
            ['product_id' => $panel585->id, 'title' => 'Standard'],
            [
                'sku' => 'PV-585W-MONO',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        $this->upsertVariantAttributes($panel585Var, [
            ['key' => 'power_watts', 'value' => '585', 'value_num' => 585],
            ['key' => 'cell_type', 'value' => 'Mono', 'value_num' => null],
            ['key' => 'module_efficiency_pct', 'value' => '21.3', 'value_num' => 21.3],
            ['key' => 'vmp_v', 'value' => '43.3', 'value_num' => 43.3],
            ['key' => 'voc_v', 'value' => '52.1', 'value_num' => 52.1],
            ['key' => 'imp_a', 'value' => '13.5', 'value_num' => 13.5],
            ['key' => 'isc_a', 'value' => '14.2', 'value_num' => 14.2],
            ['key' => 'dimensions_mm', 'value' => '2333×1134×35', 'value_num' => null],
            ['key' => 'weight_kg', 'value' => '31.5', 'value_num' => 31.5],
            ['key' => 'warranty_years', 'value' => '12', 'value_num' => 12],
        ]);

        $panel450 = Product::updateOrCreate(
            ['slug' => '450w-mono-solar-panel'],
            [
                'category_id' => $panelsCategory->id,
                'brand_id' => $trina->id,
                'name' => '450W Mono Solar Panel',
                'slug' => '450w-mono-solar-panel',
                'short_description' => 'Reliable mid-size module for residential and small commercial rooftops.',
                'description' => 'Reliable mid-size module for residential and small commercial rooftops.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $panel450Var = ProductVariant::updateOrCreate(
            ['product_id' => $panel450->id, 'title' => 'Standard'],
            [
                'sku' => 'PV-450W-MONO',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        $this->upsertVariantAttributes($panel450Var, [
            ['key' => 'power_watts', 'value' => '450', 'value_num' => 450],
            ['key' => 'cell_type', 'value' => 'Mono', 'value_num' => null],
            ['key' => 'module_efficiency_pct', 'value' => '20.9', 'value_num' => 20.9],
            ['key' => 'vmp_v', 'value' => '41.0', 'value_num' => 41.0],
            ['key' => 'voc_v', 'value' => '49.0', 'value_num' => 49.0],
            ['key' => 'imp_a', 'value' => '11.0', 'value_num' => 11.0],
            ['key' => 'isc_a', 'value' => '11.7', 'value_num' => 11.7],
            ['key' => 'dimensions_mm', 'value' => '1903×1134×30', 'value_num' => null],
            ['key' => 'weight_kg', 'value' => '23', 'value_num' => 23],
            ['key' => 'warranty_years', 'value' => '12', 'value_num' => 12],
        ]);

        $battery5k = Product::updateOrCreate(
            ['slug' => '5kwh-lithium-battery'],
            [
                'category_id' => $batteriesCategory->id,
                'brand_id' => $heatflo->id,
                'name' => '5kWh Lithium Battery',
                'slug' => '5kwh-lithium-battery',
                'short_description' => 'Lithium battery module for solar storage systems.',
                'description' => 'Lithium battery module for solar storage systems.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $battery5kVar = ProductVariant::updateOrCreate(
            ['product_id' => $battery5k->id, 'title' => '48V'],
            [
                'sku' => 'BAT-LI-5KWH-48V',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $battery5kVar->id, 'key' => 'capacity_kwh', 'value' => '5'],
            ['value_num' => 5]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $battery5kVar->id, 'key' => 'battery_voltage', 'value' => '48V'],
            ['value_num' => 48]
        );

        $this->upsertVariantAttributes($battery5kVar, [
            ['key' => 'chemistry', 'value' => 'LiFePO4', 'value_num' => null],
            ['key' => 'usable_capacity_kwh', 'value' => '4.8', 'value_num' => 4.8],
            ['key' => 'cycle_life_cycles', 'value' => '6000', 'value_num' => 6000],
            ['key' => 'max_discharge_current_a', 'value' => '100', 'value_num' => 100],
            ['key' => 'communication', 'value' => 'CAN/RS485', 'value_num' => null],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        $accessory = Product::updateOrCreate(
            ['slug' => 'pressure-relief-valve'],
            [
                'category_id' => $accessoriesCategory->id,
                'brand_id' => $heatflo->id,
                'name' => 'Pressure Relief Valve',
                'slug' => 'pressure-relief-valve',
                'short_description' => 'Demo accessory for local testing.',
                'description' => 'Demo accessory for local testing.',
                'is_active' => true,
            ]
        );

        ProductVariant::updateOrCreate(
            ['product_id' => $accessory->id, 'title' => 'Default'],
            [
                'sku' => 'PRV-DEFAULT',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        $accessoryVar = ProductVariant::where('product_id', $accessory->id)->where('title', 'Default')->first();
        if ($accessoryVar) {
            $this->upsertVariantAttributes($accessoryVar, [
                ['key' => 'material', 'value' => 'Brass', 'value_num' => null],
                ['key' => 'thread_size', 'value' => '1/2 inch', 'value_num' => null],
                ['key' => 'pressure_rating_bar', 'value' => '8', 'value_num' => 8],
                ['key' => 'temperature_rating_c', 'value' => '99', 'value_num' => 99],
            ]);
        }

        ProductCompatibility::updateOrCreate(
            ['product_id' => $accessory->id, 'category_id' => $solarCategory->id],
            ['product_id' => $accessory->id, 'category_id' => $solarCategory->id]
        );

        $mounting = Product::updateOrCreate(
            ['slug' => 'solar-panel-mounting-kit'],
            [
                'category_id' => $accessoriesCategory->id,
                'brand_id' => $heatflo->id,
                'name' => 'Solar Panel Mounting Kit',
                'slug' => 'solar-panel-mounting-kit',
                'short_description' => 'Roof mounting kit for standard PV panels.',
                'description' => 'Roof mounting kit for standard PV panels.',
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        $mountVar = ProductVariant::updateOrCreate(
            ['product_id' => $mounting->id, 'title' => 'Default'],
            [
                'sku' => 'PV-MOUNT-KIT',
                'price_cents' => 0,
                'currency' => 'USD',
                'stock_qty' => 0,
                'is_active' => true,
            ]
        );

        ProductVariantAttribute::updateOrCreate(
            ['product_variant_id' => $mountVar->id, 'key' => 'mount_type', 'value' => 'Roof'],
            ['value_num' => null]
        );

        $this->upsertVariantAttributes($mountVar, [
            ['key' => 'material', 'value' => 'Aluminium', 'value_num' => null],
            ['key' => 'roof_type', 'value' => 'IBR/Tile', 'value_num' => null],
            ['key' => 'wind_rating_kmh', 'value' => '140', 'value_num' => 140],
            ['key' => 'warranty_years', 'value' => '5', 'value_num' => 5],
        ]);

        ProductCompatibility::updateOrCreate(
            ['product_id' => $mounting->id, 'category_id' => $panelsCategory->id],
            ['product_id' => $mounting->id, 'category_id' => $panelsCategory->id]
        );
    }
}
