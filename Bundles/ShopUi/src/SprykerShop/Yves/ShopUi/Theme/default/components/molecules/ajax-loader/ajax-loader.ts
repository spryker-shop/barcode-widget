import Component from '../../../models/component';
import AjaxProvider from '../ajax-provider/ajax-provider';

export default class AjaxLoader extends Component {
    protected providers: AjaxProvider[]

    readyCallback() {
        this.providers = <AjaxProvider[]>Array.from(document.querySelectorAll(this.providerSelector));
        this.mapEvents();
    }

    protected mapEvents() {
        this.providers.forEach((provider: AjaxProvider) => {
            provider.addEventListener('fetching', (event: Event) => this.onFetching(event));
            provider.addEventListener('fetched', (event: Event) => this.onFetched(event));
        });
    }
    
    protected onFetching(event: Event): void { 
        this.classList.remove('is-invisible');
    }

    protected onFetched(event: Event): void { 
        this.classList.add('is-invisible');
    }

    get providerSelector(): string {
        return this.getAttribute('provider-selector');
    }

}
