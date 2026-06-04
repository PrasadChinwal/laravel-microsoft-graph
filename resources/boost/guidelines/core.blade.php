## Laravel Boost

This package provides an MCP (Model Context Protocol) server, AI guidelines, and agent skills that help AI agents write high-quality Laravel applications adhering to Laravel best practices. It also includes a documentation API with semantic search across 17,000+ Laravel ecosystem entries.

### Installation

```bash
composer require laravel/boost --dev
php artisan boost:install
```

@verbatim
<code-snippet name="Install and set up Laravel Boost" lang="bash">
composer require laravel/boost --dev
php artisan boost:install
</code-snippet>
@endverbatim

### MCP Server

Laravel Boost provides an MCP server exposing tools for AI agents to inspect your application, query the database, execute code, and more.

Register manually if needed:

```json
{
    "mcpServers": {
        "laravel-boost": {
            "command": "php",
            "args": ["artisan", "boost:mcp"]
        }
    }
}
```

@verbatim
<code-snippet name="Manually register the MCP server" lang="json">
{
    "mcpServers": {
        "laravel-boost": {
            "command": "php",
            "args": ["artisan", "boost:mcp"]
        }
    }
}
</code-snippet>
@endverbatim

### Available MCP Tools

| Name | Notes |
| ---- | ----- |
| Application Info | Read PHP & Laravel versions, database engine, list of ecosystem packages with versions, and Eloquent models |
| Browser Logs | Read logs and errors from the browser |
| Database Connections | Inspect available database connections, including the default connection |
| Database Query | Execute a query against the database |
| Database Schema | Read the database schema |
| Get Absolute URL | Convert relative path URIs to absolute so agents generate valid URLs |
| Last Error | Read the last error from the application's log files |
| Read Log Entries | Read the last N log entries |
| Search Docs | Query the Laravel hosted documentation API service to retrieve documentation based on installed packages |

### AI Guidelines

AI guidelines are composable instruction files loaded upfront to provide AI agents with essential context about Laravel ecosystem packages. They contain core conventions, best practices, and framework-specific patterns.

Supported packages: Laravel Framework (10.x-13.x), Livewire (2.x-4.x), Flux UI (free/pro), Inertia (1.x-3.x), Pest (3.x-4.x), Tailwind CSS (3.x-4.x), and more.

Add custom guidelines by placing `.blade.php` or `.md` files in `.ai/guidelines/*`.

### Agent Skills

Skills are targeted knowledge modules activated on-demand. They are installed automatically based on packages detected in `composer.json`.

Create custom skills by adding a `SKILL.md` file to `.ai/skills/{skill-name}/`.

```markdown
---
name: my-custom-skill
description: Build and work with custom domain features.
---

# My Custom Skill

## When to use this skill
Use this skill when working with custom domain features...
```

@verbatim
<code-snippet name="Create a custom skill" lang="markdown">
---
name: my-custom-skill
description: Build and work with custom domain features.
---

# My Custom Skill

## When to use this skill
Use this skill when working with custom domain features...
</code-snippet>
@endverbatim

### Guidelines vs. Skills

| Aspect | Guidelines | Skills |
| ------ | ---------- | ------ |
| **Loaded** | Upfront, always present | On-demand, when relevant |
| **Scope** | Broad, foundational | Focused, task-specific |
| **Purpose** | Core conventions & best practices | Detailed implementation patterns |

### Documentation API

Laravel Boost includes a Documentation API with semantic search using embeddings. The `Search Docs` MCP tool allows agents to query the Laravel hosted documentation API service to retrieve documentation based on installed packages.

Supports: Laravel Framework, Filament, Flux UI, Inertia, Livewire, Nova, Pest, Tailwind CSS.
