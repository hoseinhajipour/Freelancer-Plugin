# Advanced Project and Resume Management Plugin

A WordPress plugin to manage projects and resumes with custom post types, taxonomies, and shortcodes. This plugin allows users to create, edit, and display projects and resumes directly from the frontend.

## Features

- **Custom Post Types**: 
  - `project`: Manage and display projects.
  - `resume`: Manage and display resumes.
  
- **Taxonomy**: 
  - `skills`: Assign skills to projects and resumes.

- **Shortcodes**:
  - `[register_project]`: Create a project submission form.
  - `[register_resume]`: Create a resume submission form.

- **Frontend Templates**:
  - Custom templates for displaying projects and resumes.

- **File Attachments**: Upload and manage attachments for projects and resumes.

## Installation

1. Download the plugin files or clone this repository.
2. Upload the plugin to your WordPress site:
   - Go to **Plugins > Add New**.
   - Click **Upload Plugin** and select the plugin ZIP file.
   - Click **Install Now** and then **Activate**.
3. The plugin will be activated, and you can start using the shortcodes and features.

## Usage

### Shortcodes

1. **Register Project Form**:
   Add the `[register_project]` shortcode to any page or post to display the project submission form.

   Example:
   ```plaintext
   [register_project]
