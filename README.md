Implementation Plan - NFC Tags Management Module
The goal is to implement an "NFC Tags" management module where each tag belongs to a specific Site. These tags will have a unique identifier (UID) that can be written to physical NFC storage devices.

User Review Required
IMPORTANT

The "NFC Tags" module will be linked to the "Sites" module.

Proposed Fields for NFC Tag:

site_id: Reference to the parent site.
uid: Unique identifier for the NFC chip (e.g., "NFC-123456").
name: Friendly name for the tag (e.g., "Main Gate Entrance").
status: Active/Inactive.
Proposed Changes
[Database Layer]
[NEW] 
Migration: create_nfc_tags_table.php
Create the nfc_tags table with a foreign key back to the sites table and a unique uid field.

[Model Layer]
[NEW] 
NfcTag.php
Define the NfcTag Eloquent model with fillable attributes and its relationship to Site.

[MODIFY] 
Site.php
Add a hasMany relationship to the NfcTag model.

[Controller Layer]
[NEW] 
NfcTagController.php
Implement the following methods:

index(): Fetch and display all NFC tags, showing their Parent Site and Company.
create(): Show the NFC creation form (requires fetching all sites for the dropdown).
store(): Validate and save a new unique NFC tag.
edit($id): Show the NFC edit form.
update(Request $request, $id): Validate and update an existing tag.
delete($id): Remove a tag.
[Route Layer]
[MODIFY] 
web.php
Add CRUD routes for NFC Tags.

[View Layer]
[NEW] 
index.blade.php
Listing view for all NFC tags, showing a clear copyable UID for writing to chips.

[NEW] 
create.blade.php
Form for adding a new NFC tag, including site selection.

[NEW] 
edit.blade.php
Form for editing an existing NFC tag.

[MODIFY] 
header.blade.php
Add an "NFC" tab in the top navigation bar.

Open Questions
UID Generation: Should the UID be manually entered (if reading from a chip) or should I add a "Generate" button to create a random unique code? (I will provide both for convenience).
Physical Write: This module will provide the "Data" (UID/URL) to be written. The actual writing will depend on your NFC hardware/app.
Verification Plan
Automated Tests
Run php artisan migrate to verify the table creation.
Manual Verification
Access the "NFC" tab in the header.
Test "Create NFC Tag" and ensure UID uniqueness is enforced.
Verify "Edit", "Update", and "Delete" functionality.
Ensure the relationship chain (NFC -> Site -> Company) displays correctly in the index.







for JWT
php artisan jwt:secret