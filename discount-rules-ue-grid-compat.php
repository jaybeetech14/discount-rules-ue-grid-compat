<?php
/**
 * Plugin Name: Discount Rules - Unlimited Elements Grid Compatibility
 * Description: Displays discounted prices correctly in Unlimited Elements Woo product grids and prevents duplicate price output
 * Version: 1.4
 * Author: Jason Bearthes
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: discount-rules-ue-grid-compat
 *
 * @package Discount_Rules_UE_Grid_Compat
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Since Unlimited Elements third-party integration is now enabled,
 * we only need to ensure the prices display correctly without duplication
 */

/**
 * Remove duplicate price display - let Unlimited Elements handle it with the hook
 */
add_action( 'init', 'jbt_check_unlimited_elements_integration' );

function jbt_check_unlimited_elements_integration() {
    // Check if we're using Unlimited Elements with third-party integration
    // If so, we don't need most of our custom code

    // Only add minimal styling support
    add_action( 'wp_head', 'jbt_unlimited_elements_price_css', 999 );
}

/**
 * Clean up any duplicate price displays
 */
add_filter( 'woocommerce_get_price_html', 'jbt_prevent_duplicate_prices', 1, 2 );

function jbt_prevent_duplicate_prices( $price_html, $product ) {
    // Skip on admin and single product pages
    if ( is_admin() || is_product() ) {
        return $price_html;
    }

    // Check if we're in an Unlimited Elements context using the hook
    // The hook 'woocommerce_after_shop_loop_item_title' means UE is handling prices
    if ( did_action( 'woocommerce_before_shop_loop_item_title' ) &&
        doing_action( 'woocommerce_after_shop_loop_item_title' ) ) {
        // We're in the UE hook - let it handle the display
        return $price_html;
    }

    return $price_html;
}

/**
 * Ensure sale badge shows correctly
 */
add_filter( 'woocommerce_product_is_on_sale', 'jbt_show_sale_badge', 10, 2 );

function jbt_show_sale_badge( $on_sale, $product ) {
    // Don't override on single product pages
    if ( is_product() ) {
        return $on_sale;
    }

    if ( $on_sale ) {
        return $on_sale;
    }

    // Check if there's a sale price set by Discount Rules
    $sale_price    = $product->get_sale_price();
    $regular_price = $product->get_regular_price();

    if ( ! empty( $sale_price ) && ! empty( $regular_price ) && $sale_price < $regular_price ) {
        return true;
    }

    return $on_sale;
}

/**
 * CSS to fix the display and hide duplicate prices
 */
function jbt_unlimited_elements_price_css() {
    ?>
    <style>
        /* Hide the entire ue_grid_prices section since the price above already shows correctly */
        .ue_grid_item_bottom .ue_grid_prices {
            display: none !important;
        }

        /* Alternative: If you want to keep ue_grid_prices, hide the top price instead */
        /* Uncomment these lines and comment out the rule above if needed */
        /*
        .ue_grid_item_bottom > .price {
            display: none !important;
        }
        */

        /* If keeping ue_grid_prices, hide the duplicate regular price in it */
        .ue_grid_prices > .uc_price:last-child {
            display: none !important;
        }

        /* Style the regular (crossed out) price */
        .ue_grid_item_bottom .price del {
            opacity: 0.6;
            font-size: 0.9em;
            margin-right: 8px;
            color: #999;
            text-decoration: line-through;
        }

        /* Style the sale price with specific color */
        .ue_grid_item_bottom .price ins {
            text-decoration: none;
            font-weight: 600;
            background: none;
            color: #2AAD87 !important;
        }

        /* Target the bdi tags that contain the actual price text */
        .ue_grid_item_bottom .price ins bdi,
        .ue_grid_item_bottom .price ins .woocommerce-Price-amount bdi {
            color: #2AAD87 !important;
        }

        /* Also apply color to the price amount and all child elements */
        .ue_grid_item_bottom .price ins .woocommerce-Price-amount,
        .ue_grid_item_bottom .price ins .amount,
        .ue_grid_item_bottom .price ins span {
            color: #2AAD87 !important;
        }

        /* Target the currency symbol specifically */
        .ue_grid_item_bottom .price ins .woocommerce-Price-currencySymbol {
            color: #2AAD87 !important;
        }

        /* Broader targeting - apply to everything inside ins */
        .ue_grid_item_bottom .price ins * {
            color: #2AAD87 !important;
        }

        /* Ensure proper spacing */
        .ue_grid_item_bottom .price {
            margin: 8px 0;
        }

        /* Hide the &nbsp; and separator text in ue_grid_prices if it shows */
        .ue_grid_prices .ue-text-after-price {
            display: none !important;
        }
    </style>
    <?php
}
