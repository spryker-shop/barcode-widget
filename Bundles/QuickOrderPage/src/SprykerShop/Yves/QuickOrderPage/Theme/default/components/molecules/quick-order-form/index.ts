import './style';
import register from 'ShopUi/app/registry';
export default register('quick-order-form', () => import(/* webpackMode: "lazy" */'./quick-order-form'));
