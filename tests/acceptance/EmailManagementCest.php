<?php

declare(strict_types=1);

use Page\Acceptance\EmailsPage;

class EmailManagementCest
{
    private AcceptanceTester $I;
    public const ADMIN_PASSWORD                   = 'Maut1cR0cks!';
    public const ADMIN_USER                       = 'admin';
    public const DATE_FORMAT                      = 'Y:m:d H:i:s';

    public function _before(AcceptanceTester $I): void
    {
        $I->login(self::ADMIN_USER, self::ADMIN_PASSWORD);
    }

    public function tryToBatchChangeEmailCategory(AcceptanceTester $I): void
    {
        $this->I = $I;

        $now = date(self::DATE_FORMAT);

        // Arrange
        $I->createAContactSegment('Segment '.$now);
        $I->createACategory('Category '.$now);
        $I->createAnEmail('Email '.$now);

        // Act
        $I->amOnPage(EmailsPage::URL);
        $I->wait(1);
        $this->selectAllEmails();
        $this->selectChangeCategoryAction();
        $newCategoryName = $this->doChangeCategory();
        $I->reloadPage();

        // Assert
        $this->verifyAllEmailsBelongTo($newCategoryName);
    }

    public function selectAllEmails(): void
    {
        $I = $this->I;
        $I->waitForElementClickable(EmailsPage::SELECT_ALL_CHECKBOX);
        $I->click(EmailsPage::SELECT_ALL_CHECKBOX);
    }

    private function selectChangeCategoryAction(): void
    {
        $I = $this->I;
        $I->waitForElementClickable(EmailsPage::SELECTED_ACTIONS_DROPDOWN);
        $I->click(EmailsPage::SELECTED_ACTIONS_DROPDOWN);
        $I->waitForElementClickable(EmailsPage::CHANGE_CATEGORY_ACTION);
        $I->click(EmailsPage::CHANGE_CATEGORY_ACTION);
    }

    protected function verifyAllEmailsBelongTo(string $firstCategoryName): void
    {
        $I = $this->I;

        $categories = $I->grabMultiple('span.label-category');
        for ($i = 1; $i <= count($categories); ++$i) {
            $I->see($firstCategoryName, '//*[@id="app-content"]/div/div[2]/div[2]/div[1]/table/tbody/tr['.$i.']/td[3]/div');
        }
    }

    private function doChangeCategory(): string
    {
        $I = $this->I;

        $I->waitForElementClickable(EmailsPage::NEW_CATEGORY_DROPDOWN);
        $I->click(EmailsPage::NEW_CATEGORY_DROPDOWN);
        $I->waitForElementVisible(EmailsPage::NEW_CATEGORY_OPTION);
        $newCategoryName = $I->grabTextFrom(EmailsPage::NEW_CATEGORY_OPTION);
        $I->click(EmailsPage::NEW_CATEGORY_OPTION);
        $I->waitForElementClickable(EmailsPage::SAVE_BUTTON);
        $I->click(EmailsPage::SAVE_BUTTON);

        return $newCategoryName;
    }
}
