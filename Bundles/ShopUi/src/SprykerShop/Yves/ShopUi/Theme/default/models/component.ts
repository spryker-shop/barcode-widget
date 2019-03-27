/**
 * A Component is an extension of an HTMLElement.
 * It is used in Spryker Shop as base class for every components.
 */
export default abstract class Component extends HTMLElement {
    private isComponentMounted: boolean;

    /**
     * The name of the component.
     */
    readonly name: string;

    /**
     * The js-safe name of the component.
     */
    readonly jsName: string;

    /**
     * Creates an instance of Component.
     */
    constructor() {
        super();
        this.name = this.tagName.toLowerCase();
        this.jsName = `js-${this.name}`;
        this.isComponentMounted = false;
    }

    protected dispatchCustomEvent(name: string, detail: any = {}): void {
        const customEvent = new CustomEvent(name, { detail });
        this.dispatchEvent(customEvent);
    }

    /**
     * Same as mountCallback().
     *
     * @deprecated Use mountCallback() instead.
     */
    protected abstract readyCallback(): void;

    /**
     * Used by the application to mark the current component as mounted and avoid multiple initialisations.
     */
    markAsMounted(): void {
        this.isComponentMounted = true;
    }

    /**
     * Invoked when DOM is loaded and every webcomponent in the page is defined.
     *
     * @remarks
     * Use this method as initial point for your component if you intend to query the DOM for other webcomponents.
     * If this is not needed, you can use connectedCallback() intead for a faster execution,
     * as described by official documentation for Web Components:
     * {@link https://developer.mozilla.org/en-US/docs/Web/Web_Components/
     * Using_custom_elements#Using_the_lifecycle_callbacks}
     */
    mountCallback(): void {
        this.readyCallback();
    }

    /**
     * Gets if the component has beed mounted already.
     */
    get isMounted(): boolean {
        return this.isComponentMounted;
    }
}
