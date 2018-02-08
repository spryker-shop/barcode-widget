import Component from '../../../models/component';

export default class ClassToggler extends Component {
    trigger: HTMLInputElement
    targets: HTMLElement[]

    ready(): void {
        this.trigger = this.querySelector(`.${this.selector}__checkbox`);
        this.targets = Array.from(document.getElementsByClassName(this.target));
        this.toggle();
        this.mapEvents();
    }

    mapEvents(): void {
        this.trigger.addEventListener('change', (event: Event) => this.onTargetClick(event));
    }

    onTargetClick(event: Event): void { 
        event.preventDefault();
        this.toggle();
    }

    toggle(): void { 
        const addClass = this.hideWhenChecked ? this.trigger.checked : !this.trigger.checked;
        this.targets.forEach((element: HTMLElement) => element.classList.toggle(this.classToToggle, addClass));
    }

    set target(value: string) { 
        this.setAttributeSafe('target', value);
    }

    get target(): string {
        return this.getAttributeSafe('target');
    }

    set classToToggle(value: string) {
        this.setAttributeSafe('class-to-toggle', value);
    }

    get classToToggle(): string {
        return this.getAttributeSafe('class-to-toggle');
    }

    set hideWhenChecked(value: boolean) {
        this.setPropertySafe('hide-when-checked', value);
    }

    get hideWhenChecked(): boolean {
        return this.getPropertySafe('hide-when-checked');
    }
}
