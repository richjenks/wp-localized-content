# Localized Content

Show different content or redirect to another location based on user's location

## Usage

Regional Content has three shortcodes:

- **`[regional-echo]`**: Show specific text
- **`[regional-include]`**: Include content from other post by ID or slug
- **`[regional-redirect]`**: Redirect to another location

To specify an action for a region, add its timezone as an attribute and the result as its value.

Note that it will use the first match, so put the most specific options first, e.g. "Europe_London" before "Europe".