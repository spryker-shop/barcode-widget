Displays shopping cart as a table with action links (view, update, delete).

## Code sample

```
{% include molecule('cart-table', 'MultiCartWidget') with {
    data: {
        carts: carts,
        widgets: widgets,
        actions: {
            view: true,
            update: false,
            set_default: true,
            delete: true
        }
    }
} only %}
```