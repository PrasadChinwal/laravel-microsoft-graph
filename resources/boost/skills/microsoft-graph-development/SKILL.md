---
name: microsoft-graph-development
description: Build and work with Microsoft Graph API features, including users, calendars, events, mail, Outlook, attachments, Teams configuration, and number assignments.
when:
  - composer.json contains "prasadchinwal/microsoft-graph"
  - file contains "use PrasadChinwal\\MicrosoftGraph\\"
  - file contains "MicrosoftGraph::"
---

# Microsoft Graph API Development

## When to use this skill
Use this skill when working with the `prasadchinwal/microsoft-graph` Laravel package to interact with Microsoft 365 services including users, calendars, events, mail, Outlook, attachments, Teams configuration, and number assignments.

## Configuration

Publish the config file:
```bash
php artisan vendor:publish --tag=microsoft-graph-config
```

Required `.env` variables:
```env
MICROSOFT_GRAPH_TENANT_ID=your-tenant-id
MICROSOFT_GRAPH_CLIENT_ID=your-client-id
MICROSOFT_GRAPH_CLIENT_SECRET=your-client-secret
```

## Namespace
```php
use PrasadChinwal\MicrosoftGraph\Facades\MicrosoftGraph;
```

## Available APIs

### User Management
```php
// List all users
$users = MicrosoftGraph::users()->get();

// Find a user by email
$user = MicrosoftGraph::users()->find('john.doe@company.com');
// Access: $user->displayName, $user->jobTitle, $user->department, $user->mail, $user->id

// Update a user
$update = new \PrasadChinwal\MicrosoftGraph\Builder\User\User();
$update->displayName = 'Jane Smith';
$update->jobTitle = 'Senior Engineer';
$update->department = 'Engineering';
MicrosoftGraph::users()->update('jane.smith@company.com', $update);

// Profile photo
MicrosoftGraph::users()->withEmail('john.doe@company.com')->getPhoto();
MicrosoftGraph::users()->withEmail('john.doe@company.com')->updatePhoto($imageData);
MicrosoftGraph::users()->withEmail('john.doe@company.com')->deletePhoto();

// Licenses
$licenses = MicrosoftGraph::users()->withEmail('john.doe@company.com')->getLicenses();
foreach ($licenses as $license) {
    echo $license->skuPartNumber;
}

// Assign license
use PrasadChinwal\MicrosoftGraph\Builder\License\NewLicense;
use PrasadChinwal\MicrosoftGraph\Builder\License\NewLicenseCollection;
use PrasadChinwal\MicrosoftGraph\Builder\License\AssignLicenseBuilder;

$license = new NewLicense(disabledPlans: [], skuId: 'sku-guid-here');
$collection = new NewLicenseCollection([$license]);
$builder = new AssignLicenseBuilder(addLicenses: $collection, removeLicenses: []);
$user = MicrosoftGraph::users()->withEmail('john.doe@company.com')->assignLicense($builder);
```

### Calendar Management
```php
// List calendars
$calendars = MicrosoftGraph::calendar()->for('john.doe@company.com')->get();

// Get schedule (free/busy)
$schedule = MicrosoftGraph::calendar()->for('requester@company.com')->schedule(
    users: ['john.doe@company.com', 'jane.smith@company.com'],
    from: Carbon\Carbon::now(),
    to: Carbon\Carbon::now()->addDays(7),
    timezone: 'America/Chicago',
    interval: 60
);

// Get calendar view (events in date range)
$events = MicrosoftGraph::calendar()->for('john.doe@company.com')->view(
    start: Carbon\Carbon::now()->toIso8601String(),
    end: Carbon\Carbon::now()->addMonth()->toIso8601String()
);

// Filter calendar events
$events = MicrosoftGraph::calendar()
    ->for('john.doe@company.com')
    ->where('start/dateTime', 'ge', '2024-01-01')
    ->where('end/dateTime', 'le', '2024-12-31')
    ->view(start: '...', end: '...');
```

### Event Management
```php
// List events (with optional OData filters)
$events = MicrosoftGraph::event()->for('john.doe@company.com')->get();

// Filter events
$events = MicrosoftGraph::event()
    ->for('john.doe@company.com')
    ->where('subject', 'eq', 'Team Meeting')
    ->orWhere('subject', 'eq', 'All Hands')
    ->get();

// Create an event from a mailable
php artisan make:graph-event TeamMeetingEvent
// Then configure and use:
$response = MicrosoftGraph::event()->create(new \App\Mail\TeamMeetingEvent());

// Update event
$response = MicrosoftGraph::event()->update(
    eventId: 'AAMkAGI2...',
    mailable: new \App\Mail\TeamMeetingEvent()
);

// Accept/decline/cancel events
MicrosoftGraph::event()->for('john.doe@company.com')->accept(eventId: '...', message: 'Looking forward to it!');
MicrosoftGraph::event()->for('john.doe@company.com')->decline(eventId: '...', message: 'Unable to attend.');
MicrosoftGraph::event()->for('organizer@company.com')->cancel(eventId: '...', message: 'Meeting cancelled.');
```

### Mail Management
```php
// List messages (with OData filters)
$messages = MicrosoftGraph::mail()->for('john.doe@company.com')->top(50)->get();

// Filter messages
$messages = MicrosoftGraph::mail()
    ->for('john.doe@company.com')
    ->where('isRead', '!=', 'true')
    ->top(25)
    ->get();

$messages = MicrosoftGraph::mail()
    ->for('john.doe@company.com')
    ->where('from/emailAddress/address', '=', 'sender@example.com')
    ->where('receivedDateTime', '>=', '2024-01-01T00:00:00Z')
    ->get();
```

### Outlook / Send Email
```php
// Single recipient
MicrosoftGraph::outlook()
    ->for('sender@company.com')
    ->sendEmail(
        subject: 'Project Update',
        message: 'The project is progressing well...',
        to: 'recipient@company.com'
    );

// Multiple recipients
MicrosoftGraph::outlook()
    ->for('sender@company.com')
    ->sendEmail(
        subject: 'Team Announcement',
        message: 'Please review...',
        to: ['john.doe@company.com', 'jane.smith@company.com', 'team@company.com']
    );
```

### Attachment Management
```php
use PrasadChinwal\MicrosoftGraph\Builder\Attachment\FileAttachmentBuilder;
use PrasadChinwal\MicrosoftGraph\Builder\Attachment\ReferenceAttachmentBuilder;

// List attachments
$attachments = MicrosoftGraph::attachments()->forMessage('user@co.com', 'messageId')->list();
$attachments = MicrosoftGraph::attachments()->forEvent('user@co.com', 'eventId')->list();

// Get single attachment metadata
$attachment = MicrosoftGraph::attachments()->forMessage('user@co.com', 'msgId')->get('attachmentId');
// $attachment->id, $attachment->name, $attachment->contentType, $attachment->size

// Download raw file content
$response = MicrosoftGraph::attachments()->forMessage('user@co.com', 'msgId')->getRaw('attachmentId');
file_put_contents('file.pdf', $response->body());

// Add file attachment (max 3MB)
$builder = FileAttachmentBuilder::fromFile('/path/to/document.pdf');
// Or from raw data:
$builder = FileAttachmentBuilder::fromData(data: $imageData, name: 'photo.jpg', contentType: 'image/jpeg');
// For inline (email embeds):
$builder = FileAttachmentBuilder::fromFile('/path/to/logo.png')->asInline('logo-cid');

$attachment = MicrosoftGraph::attachments()->forMessage('user@co.com', 'msgId')->addFile($builder);

// Add reference attachment (OneDrive, SharePoint, Dropbox links)
$builder = ReferenceAttachmentBuilder::fromUrl(
    url: 'https://contoso.sharepoint.com/report.xlsx',
    name: 'Q4 Report'
)->setProvider('oneDriveBusiness')->setPermission('edit');

$attachment = MicrosoftGraph::attachments()->forMessage('user@co.com', 'msgId')->addReference($builder);

// Add custom attachment from raw array
$attachment = MicrosoftGraph::attachments()->forMessage('user@co.com', 'msgId')->create([
    '@odata.type' => '#microsoft.graph.fileAttachment',
    'name' => 'document.txt',
    'contentBytes' => base64_encode('Hello, World!'),
    'contentType' => 'text/plain',
]);

// Delete attachment
MicrosoftGraph::attachments()->forMessage('user@co.com', 'msgId')->delete('attachmentId');
MicrosoftGraph::attachments()->forEvent('user@co.com', 'eventId')->delete('attachmentId');
```

### Teams Configuration (Beta API)
```php
$configs = MicrosoftGraph::teamConfiguration()->get();
// Filter by ID:
$config = MicrosoftGraph::teamConfiguration()
    ->where(field: 'id', operator: '=', value: '109c9587-bdf8-4f84-ba53-01c7ab2efa22')
    ->get();
// Helper: check if Enterprise Voice is enabled
$config->first()->hasEnterpriseVoiceEnabled();
// Access phone numbers:
$config->first()->telephoneNumbers->pluck('telephoneNumber');
```

### Number Assignments (Beta API)
```php
$assignments = MicrosoftGraph::numberAssignement()->get();

// Filter by status
$unassigned = MicrosoftGraph::numberAssignement()
    ->where(field: 'assignmentStatus', operator: '=', value: 'unassigned')
    ->get();

// Filter by phone number
$number = MicrosoftGraph::numberAssignement()
    ->where('telephoneNumber', '!=', '+000000000')
    ->get();
```

## OData Filter Operators

The `where()` method on Event, Calendar, Mail, TeamConfiguration, and NumberAssignment endpoints supports these operators:

| Operator | Maps to |
|----------|---------|
| `=` | `eq` |
| `!=` | `ne` |
| `>` | `gt` |
| `<` | `lt` |
| `>=` | `ge` |
| `<=` | `le` |

## Exception Handling

```php
use PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException;
use PrasadChinwal\MicrosoftGraph\Exceptions\ConfigurationException;
use Illuminate\Http\Client\RequestException;

try {
    $user = MicrosoftGraph::users()->find('invalid-email');
} catch (InvalidEmailException $e) {
    // Invalid email format
} catch (ConfigurationException $e) {
    // Missing or invalid config
} catch (RequestException $e) {
    // API request failure
}
```

## Testing

Use Laravel's `Http::fake()` to mock Microsoft Graph API responses:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'graph.microsoft.com/*' => Http::response([
        'value' => [['displayName' => 'Test User', 'mail' => 'test@example.com', 'businessPhones' => []]]
    ], 200),
]);

$users = MicrosoftGraph::users()->get();
```

## Best Practices

1. **Token caching** is automatic — access tokens are cached with a 5-minute buffer before expiry.
2. To manually clear the token cache (useful in tests): `$graph->clearTokenCache()`
3. **File attachments max 3MB** — use upload session (not yet implemented) for larger files.
4. Always call `for()` or `withEmail()` before operations that need a user context.
5. Use strong OData filters on the server side rather than filtering collections in PHP.
6. The `Http::graph()` macro automatically sets the `outlook.timezone` header from your `config/microsoft-graph.timezone`.
