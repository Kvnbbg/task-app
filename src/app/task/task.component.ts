import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: "app-task",
  templateUrl: "./task.component.html",
  styleUrls: ["./task.component.css"],
})
export class AppComponent {
[x: string]: any;
  title = 'Error Handling App';
  errorMessage: string = '';

  constructor(private http: HttpClient) {}

  ngOnInit() {
    console.log("TaskComponent initialized");
    this.fetchError();
  }

  fetchError() {
    // Example URL, replace with your actual endpoint
    this.http.get<{error: string}>('/api/error')
      .subscribe(
        response => this.errorMessage = response.error,
        error => this.errorMessage = 'Failed to fetch error details.'
      );
  }
}


