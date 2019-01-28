Displays brief information about owner, access, and number of users or business units whom access was shared with.

## Code sample 

```
{% include molecule('shopping-list-info', 'ShoppingListPage') with {
    data: {
        shoppingList: data.shoppingList
    }
} only %}
```
