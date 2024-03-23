import { Component } from '@angular/core';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    title = 'My App';

    constructor() {
        // Initialize component
        console.log("AppComponent initialized");
    }

    ngOnInit() {
        // Component initialization logic
        console.log("AppComponent ngOnInit");
    }
}