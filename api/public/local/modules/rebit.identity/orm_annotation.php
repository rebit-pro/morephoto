<?php

// ORMENTITYANNOTATION:Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable

namespace Rebit\Identity\Domain\ApiConnection\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * ApiConnection
     *
     * @see ApiConnectionTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                      getId()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                      hasId()
     * @method bool                                                      isIdFilled()
     * @method bool                                                      isIdChanged()
     * @method \int                                                      getUfUserId()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfUserId(\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                                                      hasUfUserId()
     * @method bool                                                      isUfUserIdFilled()
     * @method bool                                                      isUfUserIdChanged()
     * @method \int                                                      remindActualUfUserId()
     * @method \int                                                      requireUfUserId()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfUserId()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfUserId()
     * @method \int                                                      fillUfUserId()
     * @method \string                                                   getUfApiKeyEncrypted()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfApiKeyEncrypted(\Bitrix\Main\DB\SqlExpression|\string $ufApiKeyEncrypted)
     * @method bool                                                      hasUfApiKeyEncrypted()
     * @method bool                                                      isUfApiKeyEncryptedFilled()
     * @method bool                                                      isUfApiKeyEncryptedChanged()
     * @method \string                                                   remindActualUfApiKeyEncrypted()
     * @method \string                                                   requireUfApiKeyEncrypted()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfApiKeyEncrypted()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfApiKeyEncrypted()
     * @method \string                                                   fillUfApiKeyEncrypted()
     * @method \string                                                   getUfSecretKeyEncrypted()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfSecretKeyEncrypted(\Bitrix\Main\DB\SqlExpression|\string $ufSecretKeyEncrypted)
     * @method bool                                                      hasUfSecretKeyEncrypted()
     * @method bool                                                      isUfSecretKeyEncryptedFilled()
     * @method bool                                                      isUfSecretKeyEncryptedChanged()
     * @method \string                                                   remindActualUfSecretKeyEncrypted()
     * @method \string                                                   requireUfSecretKeyEncrypted()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfSecretKeyEncrypted()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfSecretKeyEncrypted()
     * @method \string                                                   fillUfSecretKeyEncrypted()
     * @method \string                                                   getUfMode()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfMode(\Bitrix\Main\DB\SqlExpression|\string $ufMode)
     * @method bool                                                      hasUfMode()
     * @method bool                                                      isUfModeFilled()
     * @method bool                                                      isUfModeChanged()
     * @method \string                                                   remindActualUfMode()
     * @method \string                                                   requireUfMode()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfMode()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfMode()
     * @method \string                                                   fillUfMode()
     * @method \string                                                   getUfStatus()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfStatus(\Bitrix\Main\DB\SqlExpression|\string $ufStatus)
     * @method bool                                                      hasUfStatus()
     * @method bool                                                      isUfStatusFilled()
     * @method bool                                                      isUfStatusChanged()
     * @method \string                                                   remindActualUfStatus()
     * @method \string                                                   requireUfStatus()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfStatus()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfStatus()
     * @method \string                                                   fillUfStatus()
     * @method \Bitrix\Main\Type\DateTime                                getUfLastVerifiedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfLastVerifiedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufLastVerifiedAt)
     * @method bool                                                      hasUfLastVerifiedAt()
     * @method bool                                                      isUfLastVerifiedAtFilled()
     * @method bool                                                      isUfLastVerifiedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                                remindActualUfLastVerifiedAt()
     * @method \Bitrix\Main\Type\DateTime                                requireUfLastVerifiedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfLastVerifiedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfLastVerifiedAt()
     * @method \Bitrix\Main\Type\DateTime                                fillUfLastVerifiedAt()
     * @method \string                                                   getUfErrorMessage()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfErrorMessage(\Bitrix\Main\DB\SqlExpression|\string $ufErrorMessage)
     * @method bool                                                      hasUfErrorMessage()
     * @method bool                                                      isUfErrorMessageFilled()
     * @method bool                                                      isUfErrorMessageChanged()
     * @method \string                                                   remindActualUfErrorMessage()
     * @method \string                                                   requireUfErrorMessage()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfErrorMessage()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfErrorMessage()
     * @method \string                                                   fillUfErrorMessage()
     * @method \Bitrix\Main\Type\DateTime                                getUfCreatedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfCreatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                                                      hasUfCreatedAt()
     * @method bool                                                      isUfCreatedAtFilled()
     * @method bool                                                      isUfCreatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                                remindActualUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                                requireUfCreatedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfCreatedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                                fillUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                                getUfUpdatedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection setUfUpdatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                                                      hasUfUpdatedAt()
     * @method bool                                                      isUfUpdatedAtFilled()
     * @method bool                                                      isUfUpdatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                                remindActualUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                                requireUfUpdatedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection resetUfUpdatedAt()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection unsetUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                                fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity     $entity
     * @property array      $primary
     * @property int        $state       @see \Bitrix\Main\ORM\Objectify\State
     * @property Dictionary $customData
     * @property Context    $authContext
     *
     * @method        mixed                                                                                           get($fieldName)
     * @method        mixed                                                                                           remindActual($fieldName)
     * @method        mixed                                                                                           require($fieldName)
     * @method        bool                                                                                            has($fieldName)
     * @method        bool                                                                                            isFilled($fieldName)
     * @method        bool                                                                                            isChanged($fieldName)
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection                                       set($fieldName, $value)
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection                                       reset($fieldName)
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection                                       unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection                                       wakeUp($data)
     */
    class EO_ApiConnection
    {
        // @var \Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable
        public static $dataClass = '\Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Identity\Domain\ApiConnection\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * ApiConnectionCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \int[]                       getUfUserIdList()
     * @method \int[]                       fillUfUserId()
     * @method \string[]                    getUfApiKeyEncryptedList()
     * @method \string[]                    fillUfApiKeyEncrypted()
     * @method \string[]                    getUfSecretKeyEncryptedList()
     * @method \string[]                    fillUfSecretKeyEncrypted()
     * @method \string[]                    getUfModeList()
     * @method \string[]                    fillUfMode()
     * @method \string[]                    getUfStatusList()
     * @method \string[]                    fillUfStatus()
     * @method \Bitrix\Main\Type\DateTime[] getUfLastVerifiedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfLastVerifiedAt()
     * @method \string[]                    getUfErrorMessageList()
     * @method \string[]                    fillUfErrorMessage()
     * @method \Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime[] getUfUpdatedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                                add(\Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection $object)
     * @method        bool                                                                has(\Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection $object)
     * @method        bool                                                                hasByPrimary($primary)
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           getByPrimary($primary)
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection[]         getAll()
     * @method        bool                                                                remove(\Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection $object)
     * @method        void                                                                removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection                    fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                        save($ignoreEvents = false)
     * @method        void                                                                offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                                offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                                offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                                offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                                rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           current()                                                                                                                                                      Iterator
     * @method        mixed                                                               key()                                                                                                                                                          Iterator
     * @method        void                                                                next()                                                                                                                                                         Iterator
     * @method        bool                                                                valid()                                                                                                                                                        Iterator
     * @method        int                                                                 count()                                                                                                                                                        Countable
     * @method        \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection merge(?\Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection $collection)
     * @method        bool                                                                isEmpty()
     * @method        array                                                               collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_ApiConnection_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable
        public static $dataClass = '\Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable';
    }
}

namespace Rebit\Identity\Domain\ApiConnection\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_ApiConnection_Query                                              query()
     * @method static EO_ApiConnection_Result                                             getByPrimary($primary, array $parameters = [])
     * @method static EO_ApiConnection_Result                                             getById($id)
     * @method static EO_ApiConnection_Result                                             getList(array $parameters = [])
     * @method static EO_ApiConnection_Entity                                             getEntity()
     * @method static \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           createObject($setDefaultValues = true)
     * @method static \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection createCollection()
     * @method static \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           wakeUpObject($row)
     * @method static \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection wakeUpCollection($rows)
     */
    class ApiConnectionTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_ApiConnection_Result                                             exec()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           fetchObject()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection fetchCollection()
     */
    class EO_ApiConnection_Query extends Query {}
    /**
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           fetchObject()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection fetchCollection()
     */
    class EO_ApiConnection_Result extends Result {}
    /**
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           createObject($setDefaultValues = true)
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection createCollection()
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection           wakeUpObject($row)
     * @method \Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection wakeUpCollection($rows)
     */
    class EO_ApiConnection_Entity extends Entity {}
}
