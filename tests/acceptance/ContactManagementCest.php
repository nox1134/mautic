<?php

class ContactManagementCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->login('admin', 'Maut1cR0cks!');
    }

    public function createContactFromQuickAdd(AcceptanceTester $I)
    {
        // Navigate to the contacts page
        $I->amOnPage('/s/contacts');

        // Wait for and click on the "Quick Add" button
        $I->waitForElementClickable('#toolbar .quickadd', 30);
        $I->click('#toolbar .quickadd');

        // Wait for the Quick Add modal to appear and verify its presence
        $I->waitForElementVisible('#MauticSharedModal-label', 30);
        $I->see('Quick Add', 'h4.modal-title');

        // Wait for the form fields to be visible
        $I->waitForElementVisible('#lead_firstname', 10);

        // Fill out the quick add form
        $I->fillField('#lead_firstname', 'QuickAddFirstName');
        $I->fillField('#lead_lastname', 'QuickAddLastName');
        $I->fillField('#lead_email', 'quickadd@example.com');
        $I->fillField('#lead_tags_chosen input', 'TestTag');
        $I->pressKey('#lead_tags_chosen input', Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->fillField('#lead_companies_chosen input', 'Mautic');
        $I->pressKey('#lead_companies_chosen input', Facebook\WebDriver\WebDriverKeys::ENTER);

        // Wait for the save button to be clickable and submit the form
        $I->waitForElementClickable('#MauticSharedModal > div > div > div.modal-footer > div > button.btn.btn-default.btn-save.btn-copy', 30);
        $I->click('#MauticSharedModal > div > div > div.modal-footer > div > button.btn.btn-default.btn-save.btn-copy');
        $I->waitForElementNotVisible('#MauticSharedModal-label', 30);

        $I->amOnPage('/s/contacts');
        $I->reloadPage(); // Ensure the latest data is loaded

        // Search for the contact we just created
        $I->fillField('#list-search', 'QuickAddFirstName');
        $I->pressKey('#list-search', Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->waitForElementVisible('#leadTable', 10); // Wait for the search results to appear

        // Verify the contact is in the list
        $I->see('QuickAddFirstName', '#leadTable');

        // Clear the search
        $I->click('#btn-filter');
    }

    public function createContactFromForm(AcceptanceTester $I)
    {
        // Navigate to the contacts page
        $I->amOnPage('/s/contacts');

        // Click on "+New" button
        $I->waitForElementClickable('#toolbar a:nth-child(2)', 30);
        $I->click('#toolbar a:nth-child(2)');
        $I->waitForText('New Contact', 30);

        // Fill out the form fields
        $I->waitForElementVisible('#lead_firstname', 10);
        $I->fillField('#lead_firstname', 'FirstName');
        $I->fillField('#lead_lastname', 'LastName');
        $I->fillField('#lead_email', 'email@example.com');
        $I->fillField('#lead_tags_chosen input', 'TestTag');
        $I->pressKey('#lead_tags_chosen input', Facebook\WebDriver\WebDriverKeys::ENTER);

        // Scroll back to the top of the page
        $I->executeJS('window.scrollTo(0, 0);');

        // Click the save and close button
        $I->waitForElementClickable('#lead_buttons_save_toolbar', 30);
        $I->click('#lead_buttons_save_toolbar');

        // Wait for the contact details page to load
        $I->waitForElementVisible('.page-header-title .span-block', 30);
        $I->see('FirstName LastName', '.page-header-title .span-block');

        // Click the close button on the contact details page
        $I->waitForElementClickable('#toolbar > div.std-toolbar.btn-group > a:nth-child(3)', 30);
        $I->click('a.btn.btn-default[href="/s/contacts"]');
    }

    public function acessEditContactFormFromList(AcceptanceTester $I)
    {
        // Navigate to the contacts page
        $I->amOnPage('/s/contacts');

        // Grab the name of the first contact in the list
        $contactName = $I->grabTextFrom('#leadTable tbody tr:first-child td:nth-child(2) a div');

        // Check we can see the first contact name
        $I->see($contactName);

        // Click on the dropdown caret on the first contact
        $I->click('#leadTable tbody tr:first-child td:first-child div div button');

        // Wait for the dropdown menu to show and click the delete menu option
        $I->waitForElementClickable('#leadTable > tbody > tr:nth-child(1) > td:nth-child(1) > div > div > ul > li:nth-child(1) > a', 30);
        $I->click('#leadTable > tbody > tr:nth-child(1) > td:nth-child(1) > div > div > ul > li:nth-child(1) > a');

        // Wait for the edit form to be visible
        $I->waitForElementVisible('#core > div.pa-md.bg-light-xs.bdr-b > h4', 30);
        $I->see("Edit $contactName");

        // Close the edit form (No changes are made)
        $I->click('#lead_buttons_cancel_toolbar');
    }

    public function editContactFromProfile(AcceptanceTester $I)
    {
        // Navigate to the contacts page
        $I->amOnPage('/s/contacts');

        // Grab the name of the first contact in the list
        $contactName = $I->grabTextFrom('#leadTable > tbody > tr:nth-child(1) > td:nth-child(2) > a > div');

        // Click on the contact name to view the contact details
        $I->click(['link' => $contactName]);

        // Wait for the contact details page to load and confirm we're on the correct page
        $I->waitForText($contactName, 30);
        $I->see($contactName);

        // Click on the edit button
        $I->click('#toolbar > div.std-toolbar.btn-group > a:nth-child(1)');

        // Wait for the edit form to be visible
        $I->waitForElementVisible('#core > div.pa-md.bg-light-xs.bdr-b > h4', 30);
        $I->see("Edit $contactName");

        // Edit the first and last names
        $I->fillField('#lead_firstname', 'Edited-First-Name');
        $I->fillField('#lead_lastname', 'Edited-Last-Name');

        // Save and close the form
        $I->waitForElementClickable('#lead_buttons_save_toolbar', 30);
        $I->click('#lead_buttons_save_toolbar');

        // Verify the update message
        $I->waitForText('Edited-First-Name Edited-Last-Name has been updated!', 30);
        $I->see('Edited-First-Name Edited-Last-Name has been updated!');
    }

    public function deleteContactFromList(AcceptanceTester $I)
    {
        // Navigate to the contacts page
        $I->amOnPage('/s/contacts');

        // Grab the name of the first contact in the list
        $contactName = $I->grabTextFrom('#leadTable tbody tr:first-child td:nth-child(2) a div');

        // Check we can see the first contact name
        $I->see($contactName);

        // Click on the dropdown caret on the first contact
        $I->click('#leadTable > tbody > tr:nth-child(1) > td:nth-child(1) > div > div > button');

        // Wait for the dropdown menu to show and click the delete menu option
        $I->waitForElementVisible('#leadTable > tbody > tr:nth-child(1) > td:nth-child(1) > div > div > ul > li:nth-child(4) > a', 5);
        $I->click('#leadTable > tbody > tr:nth-child(1) > td:nth-child(1) > div > div > ul > li:nth-child(4) > a');

        // Wait for the modal to show and confirm deletion
        $I->waitForElementVisible('button.btn.btn-danger', 5);
        $I->click('button.btn.btn-danger');

        // Wait for the delete confirmation message
        $I->waitForText("$contactName has been deleted!", 30);
        $I->see("$contactName has been deleted!");
    }

    public function deleteContactFromProfile(AcceptanceTester $I)
    {
        // Navigate to the contacts page
        $I->amOnPage('/s/contacts');

        // Grab the name of the first contact in the list
        $contactName = $I->grabTextFrom('#leadTable tbody tr:first-child td:nth-child(2) a div');

        // Click on the contact name to view the contact details
        $I->click(['link' => $contactName]);

        // Wait for the contact details page to load and confirm we're on the correct page
        $I->waitForText($contactName, 30);
        $I->see($contactName);

        // Click the dropdown caret to show the delete option
        $I->click('#toolbar .std-toolbar.btn-group > button > i');

        // Wait for the dropdown to be displayed and click on the delete option
        $I->waitForElementVisible('#toolbar .std-toolbar.btn-group.open > ul', 30);
        $I->click('#toolbar .std-toolbar.btn-group.open > ul > li:nth-child(5) > a');

        // Wait for the modal to become visible and click on the button to confirm delete
        $I->waitForElementVisible('button.btn.btn-danger', 30);
        $I->click('button.btn.btn-danger');

        // Wait for the delete to be completed and confirm the contact is deleted
        $I->waitForText("$contactName has been deleted!", 30);
        $I->see("$contactName has been deleted!");
    }
}
