# Discount Rules – Unlimited Elements Grid Compatibility

A WooCommerce compatibility plugin that makes discounted prices display correctly inside
[Unlimited Elements](https://unlimited-elements.com/) product grids when using a dynamic
discount-rules plugin — without duplicate price output.

## The problem

When a discount-rules plugin changes product prices at runtime, Unlimited Elements'
product grid renders the price twice: once through its own `ue_grid_prices` markup and
once through WooCommerce's standard price HTML. The result is duplicated, unstyled, or
missing sale pricing on grid/archive pages.

## What this plugin does

- **Prevents duplicate prices** — detects when Unlimited Elements is rendering inside
  `woocommerce_after_shop_loop_item_title` and lets it own the price display instead of
  fighting it (`woocommerce_get_price_html`, priority 1).
- **Fixes the sale badge** — forces `woocommerce_product_is_on_sale` to return true when
  a valid sale price exists, so the "Sale!" badge renders on grid items even when the
  discount is applied dynamically rather than stored.
- **Cleans up the grid CSS** — hides the redundant `ue_grid_prices` block and styles the
  `<del>` / `<ins>` price markup (muted strikethrough regular price, highlighted sale
  price) so discounted grids look intentional.
- **Stays out of the way** — skips admin screens and single product pages entirely; the
  shop-loop-only logic means zero impact on the rest of the store.

## Requirements

- WordPress 5.0+
- PHP 7.2+
- WooCommerce
- A discount-rules plugin that sets sale prices dynamically
- Unlimited Elements (product grid widget)

## Installation

1. Copy `discount-rules-ue-grid-compat.php` into `wp-content/plugins/discount-rules-ue-grid-compat/`.
2. Activate **Discount Rules - Unlimited Elements Grid Compatibility** in **Plugins**.
3. No settings — it works on activation.

## How it works

The plugin hooks `init` and defers to Unlimited Elements' third-party integration where
possible, keeping its own footprint to three surgical interventions: a price-HTML guard,
a sale-badge correction, and a late (`wp_head`, priority 999) stylesheet. The commented
CSS alternatives at the top of the style block document the two display strategies
(hide UE's price block vs. hide WooCommerce's) so the next developer can flip the
approach without reverse-engineering it.

## Notes

- All function names are prefixed `jbt_` to avoid collisions.
- Built with AI-assisted development (Claude), reviewed and tested on a live WooCommerce store.
