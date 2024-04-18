# Laravel Microsoft Graph

A wrapper to integrate Microsoft Graph Api's to a Laravel Application.

## Installation
- `composer require prasadchinwal/microsoft-graph`
- Run `php artisan vendor:publish` and publish the config file.
- Edit the `config/microsoft-graph.php` file to configure your settings.
  You need Tenant, Client ID and Client Secret.


## Usage:

---

### **User API**

1. Get users: [Documentation](https://learn.microsoft.com/en-us/graph/api/user-list?view=graph-rest-1.0&tabs=http)
   > Retrieves all the users.
    ```php
    $graph = MicrosoftGraph::users()->get();
    dd($graph);
    ```

2. Get user from ID: [Documentation](https://learn.microsoft.com/en-us/graph/api/user-get?view=graph-rest-1.0&tabs=http)
   > Retrieves a user from principalName.
    ```php
    $graph = MicrosoftGraph::users()
   ->find('abc@user.com');
    dd($graph);
    ```

---  

### **Calendar Api**

---

1. Get user Calendars: [Documentation](https://learn.microsoft.com/en-us/graph/api/user-list-calendars?view=graph-rest-1.0&tabs=http)
   > Retrieves the users Calendar.
    ```php
    $graph = MicrosoftGraph::calendar()
        ->for('user_email')
        ->get();
    dd($graph->first()->name); // To retrieve the name of the Calendar
    ```

2. Get user Schedule : [Documentation](https://learn.microsoft.com/en-us/graph/api/calendar-getschedule?view=graph-rest-1.0&tabs=http)
   > Retrieves the users Schedule.
    ```php
    $users = ['usera@abc.com', 'userb@edf.com'];
    $graph = MicrosoftGraph::calendar()
        ->for('requester@xyz.com')
        ->schedule(users:$users, from: Carbon::now(), to: Carbon::now()->addDays(2), timezone: 'UTC', interval: 60);
    dd($graph);
    ```

---

### **Events API**

---

1. Get user Calendar events : [Documentation](https://learn.microsoft.com/en-us/graph/api/user-list-calendars?view=graph-rest-1.0&tabs=http)
   > Retrieves the users Calendar Events.
    ```php
    $graph = MicrosoftGraph::event()
        ->for('abc@xyz.com')
        ->get();
    dd($graph->first()->subject); // To retrieve subject of first event.
    ```
   > You can also filter the events:
   >
   > For List of all filters please refer to docs [https://learn.microsoft.com/en-us/graph/filter-query-parameter?tabs=http](https://learn.microsoft.com/en-us/graph/filter-query-parameter?tabs=http).
    ```php
    $graph = MicrosoftGraph::event()
            ->for('abc@xyz.com')
            ->where("start/dateTime", "ge", "2024-04-17")
            ->where("end/dateTime", "le", "2024-04-26")
            ->get();
    ```

2. Create new Event for a user : [Documentation](https://learn.microsoft.com/en-us/graph/api/user-post-events?view=graph-rest-1.0&tabs=http)
   > Creates an event in calendar for the user.  
  - Generate Event class using
    `php artisan make:graph-event TestGraphEvent`
  - Edit the Event class as required filling in necessary details about the event.
   ```php
    $graph = MicrosoftGraph::event()
   ->create(new TestGraphEvent());
    dd($graph);
   ```

3. Update an existing Event for a user : [Documentation](https://learn.microsoft.com/en-us/graph/api/event-update?view=graph-rest-1.0&tabs=http)
   > Updates an event in calendar for the user.
- Edit the Event class as required filling in necessary details about the event.
   ```php
    $graph = MicrosoftGraph::event()
        ->update(
                new TestGraphEvent(),
                eventId:'AAMk'
            );
    dd($graph);
   ```

4. Cancel an event for a user : [Documentation](https://learn.microsoft.com/en-us/graph/api/event-cancel?view=graph-rest-1.0&tabs=http)
   > Cancels the event in calendar for the user.
   ```php
    $graph = MicrosoftGraph::event()
    ->for('pchin3@uisad.uis.edu')
    ->cancel(eventId: 'AAMk', message:"Cancelling via web request!");
    dd($graph);
   ```

5. Accept an event for a user : [Documentation](https://learn.microsoft.com/en-us/graph/api/event-accept?view=graph-rest-1.0&tabs=http)
   > Accept the event in calendar for the user.
   ```php
    $graph = MicrosoftGraph::event()
        ->for('abc@event.com')
        ->accept(eventId: 'AAMk', message:"Accepting via web request!");
    dd($graph);
   ```

6. Decline an event for a user : [Documentation](https://learn.microsoft.com/en-us/graph/api/event-decline?view=graph-rest-1.0&tabs=http)
   > Decline the event in calendar for the user.
- Edit the Event class as required filling in necessary details about the event.
   ```php
    $graph = MicrosoftGraph::event()
        ->for('abc@event.com')
        ->decline(eventId: 'AAMk', message:"Declining via web request!");
    dd($graph);
   ```
