Shows html tag element, which includes json data as an html attribute.

## Code sample 

```
{% include molecule('measurement-quantity-selector', 'ProductMeasurementUnitWidget') with {
    data: {
        json: data.json
    }
} only %}
```