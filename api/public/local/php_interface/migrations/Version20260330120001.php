<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Sprint\Migration\Exceptions\HelperException;

final class Version20260330120001 extends Version
{
    protected $author = 'copilot';

    protected $description = 'Создание группы пользователей COUNTERPARTIES';

    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();

        $helper->UserGroup()->saveGroup('COUNTERPARTIES', [
            'NAME' => 'Контрагенты',
            'ACTIVE' => 'Y',
            'C_SORT' => 200,
            'DESCRIPTION' => 'Техническая группа контрагентов Bybit',
        ]);
    }

    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();

        $helper->UserGroup()->deleteGroup('COUNTERPARTIES');
    }
}
