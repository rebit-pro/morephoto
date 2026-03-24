<?php

// ORMENTITYANNOTATION:Bitrix\Iblock\IblockTable

namespace Bitrix\Iblock {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * Iblock
     *
     * @see IblockTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                  getId()
     * @method \Bitrix\Iblock\Iblock                 setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                  hasId()
     * @method bool                                  isIdFilled()
     * @method bool                                  isIdChanged()
     * @method \Bitrix\Main\Type\DateTime            getTimestampX()
     * @method \Bitrix\Iblock\Iblock                 setTimestampX(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $timestampX)
     * @method bool                                  hasTimestampX()
     * @method bool                                  isTimestampXFilled()
     * @method bool                                  isTimestampXChanged()
     * @method \Bitrix\Main\Type\DateTime            remindActualTimestampX()
     * @method \Bitrix\Main\Type\DateTime            requireTimestampX()
     * @method \Bitrix\Iblock\Iblock                 resetTimestampX()
     * @method \Bitrix\Iblock\Iblock                 unsetTimestampX()
     * @method \Bitrix\Main\Type\DateTime            fillTimestampX()
     * @method \string                               getIblockTypeId()
     * @method \Bitrix\Iblock\Iblock                 setIblockTypeId(\Bitrix\Main\DB\SqlExpression|\string $iblockTypeId)
     * @method bool                                  hasIblockTypeId()
     * @method bool                                  isIblockTypeIdFilled()
     * @method bool                                  isIblockTypeIdChanged()
     * @method \string                               remindActualIblockTypeId()
     * @method \string                               requireIblockTypeId()
     * @method \Bitrix\Iblock\Iblock                 resetIblockTypeId()
     * @method \Bitrix\Iblock\Iblock                 unsetIblockTypeId()
     * @method \string                               fillIblockTypeId()
     * @method \string                               getLid()
     * @method \Bitrix\Iblock\Iblock                 setLid(\Bitrix\Main\DB\SqlExpression|\string $lid)
     * @method bool                                  hasLid()
     * @method bool                                  isLidFilled()
     * @method bool                                  isLidChanged()
     * @method \string                               remindActualLid()
     * @method \string                               requireLid()
     * @method \Bitrix\Iblock\Iblock                 resetLid()
     * @method \Bitrix\Iblock\Iblock                 unsetLid()
     * @method \string                               fillLid()
     * @method \string                               getCode()
     * @method \Bitrix\Iblock\Iblock                 setCode(\Bitrix\Main\DB\SqlExpression|\string $code)
     * @method bool                                  hasCode()
     * @method bool                                  isCodeFilled()
     * @method bool                                  isCodeChanged()
     * @method \string                               remindActualCode()
     * @method \string                               requireCode()
     * @method \Bitrix\Iblock\Iblock                 resetCode()
     * @method \Bitrix\Iblock\Iblock                 unsetCode()
     * @method \string                               fillCode()
     * @method \string                               getApiCode()
     * @method \Bitrix\Iblock\Iblock                 setApiCode(\Bitrix\Main\DB\SqlExpression|\string $apiCode)
     * @method bool                                  hasApiCode()
     * @method bool                                  isApiCodeFilled()
     * @method bool                                  isApiCodeChanged()
     * @method \string                               remindActualApiCode()
     * @method \string                               requireApiCode()
     * @method \Bitrix\Iblock\Iblock                 resetApiCode()
     * @method \Bitrix\Iblock\Iblock                 unsetApiCode()
     * @method \string                               fillApiCode()
     * @method \boolean                              getRestOn()
     * @method \Bitrix\Iblock\Iblock                 setRestOn(\Bitrix\Main\DB\SqlExpression|\boolean $restOn)
     * @method bool                                  hasRestOn()
     * @method bool                                  isRestOnFilled()
     * @method bool                                  isRestOnChanged()
     * @method \boolean                              remindActualRestOn()
     * @method \boolean                              requireRestOn()
     * @method \Bitrix\Iblock\Iblock                 resetRestOn()
     * @method \Bitrix\Iblock\Iblock                 unsetRestOn()
     * @method \boolean                              fillRestOn()
     * @method \string                               getName()
     * @method \Bitrix\Iblock\Iblock                 setName(\Bitrix\Main\DB\SqlExpression|\string $name)
     * @method bool                                  hasName()
     * @method bool                                  isNameFilled()
     * @method bool                                  isNameChanged()
     * @method \string                               remindActualName()
     * @method \string                               requireName()
     * @method \Bitrix\Iblock\Iblock                 resetName()
     * @method \Bitrix\Iblock\Iblock                 unsetName()
     * @method \string                               fillName()
     * @method \boolean                              getActive()
     * @method \Bitrix\Iblock\Iblock                 setActive(\Bitrix\Main\DB\SqlExpression|\boolean $active)
     * @method bool                                  hasActive()
     * @method bool                                  isActiveFilled()
     * @method bool                                  isActiveChanged()
     * @method \boolean                              remindActualActive()
     * @method \boolean                              requireActive()
     * @method \Bitrix\Iblock\Iblock                 resetActive()
     * @method \Bitrix\Iblock\Iblock                 unsetActive()
     * @method \boolean                              fillActive()
     * @method \int                                  getSort()
     * @method \Bitrix\Iblock\Iblock                 setSort(\Bitrix\Main\DB\SqlExpression|\int $sort)
     * @method bool                                  hasSort()
     * @method bool                                  isSortFilled()
     * @method bool                                  isSortChanged()
     * @method \int                                  remindActualSort()
     * @method \int                                  requireSort()
     * @method \Bitrix\Iblock\Iblock                 resetSort()
     * @method \Bitrix\Iblock\Iblock                 unsetSort()
     * @method \int                                  fillSort()
     * @method \string                               getListPageUrl()
     * @method \Bitrix\Iblock\Iblock                 setListPageUrl(\Bitrix\Main\DB\SqlExpression|\string $listPageUrl)
     * @method bool                                  hasListPageUrl()
     * @method bool                                  isListPageUrlFilled()
     * @method bool                                  isListPageUrlChanged()
     * @method \string                               remindActualListPageUrl()
     * @method \string                               requireListPageUrl()
     * @method \Bitrix\Iblock\Iblock                 resetListPageUrl()
     * @method \Bitrix\Iblock\Iblock                 unsetListPageUrl()
     * @method \string                               fillListPageUrl()
     * @method \string                               getDetailPageUrl()
     * @method \Bitrix\Iblock\Iblock                 setDetailPageUrl(\Bitrix\Main\DB\SqlExpression|\string $detailPageUrl)
     * @method bool                                  hasDetailPageUrl()
     * @method bool                                  isDetailPageUrlFilled()
     * @method bool                                  isDetailPageUrlChanged()
     * @method \string                               remindActualDetailPageUrl()
     * @method \string                               requireDetailPageUrl()
     * @method \Bitrix\Iblock\Iblock                 resetDetailPageUrl()
     * @method \Bitrix\Iblock\Iblock                 unsetDetailPageUrl()
     * @method \string                               fillDetailPageUrl()
     * @method \string                               getSectionPageUrl()
     * @method \Bitrix\Iblock\Iblock                 setSectionPageUrl(\Bitrix\Main\DB\SqlExpression|\string $sectionPageUrl)
     * @method bool                                  hasSectionPageUrl()
     * @method bool                                  isSectionPageUrlFilled()
     * @method bool                                  isSectionPageUrlChanged()
     * @method \string                               remindActualSectionPageUrl()
     * @method \string                               requireSectionPageUrl()
     * @method \Bitrix\Iblock\Iblock                 resetSectionPageUrl()
     * @method \Bitrix\Iblock\Iblock                 unsetSectionPageUrl()
     * @method \string                               fillSectionPageUrl()
     * @method \string                               getCanonicalPageUrl()
     * @method \Bitrix\Iblock\Iblock                 setCanonicalPageUrl(\Bitrix\Main\DB\SqlExpression|\string $canonicalPageUrl)
     * @method bool                                  hasCanonicalPageUrl()
     * @method bool                                  isCanonicalPageUrlFilled()
     * @method bool                                  isCanonicalPageUrlChanged()
     * @method \string                               remindActualCanonicalPageUrl()
     * @method \string                               requireCanonicalPageUrl()
     * @method \Bitrix\Iblock\Iblock                 resetCanonicalPageUrl()
     * @method \Bitrix\Iblock\Iblock                 unsetCanonicalPageUrl()
     * @method \string                               fillCanonicalPageUrl()
     * @method \int                                  getPicture()
     * @method \Bitrix\Iblock\Iblock                 setPicture(\Bitrix\Main\DB\SqlExpression|\int $picture)
     * @method bool                                  hasPicture()
     * @method bool                                  isPictureFilled()
     * @method bool                                  isPictureChanged()
     * @method \int                                  remindActualPicture()
     * @method \int                                  requirePicture()
     * @method \Bitrix\Iblock\Iblock                 resetPicture()
     * @method \Bitrix\Iblock\Iblock                 unsetPicture()
     * @method \int                                  fillPicture()
     * @method \string                               getDescription()
     * @method \Bitrix\Iblock\Iblock                 setDescription(\Bitrix\Main\DB\SqlExpression|\string $description)
     * @method bool                                  hasDescription()
     * @method bool                                  isDescriptionFilled()
     * @method bool                                  isDescriptionChanged()
     * @method \string                               remindActualDescription()
     * @method \string                               requireDescription()
     * @method \Bitrix\Iblock\Iblock                 resetDescription()
     * @method \Bitrix\Iblock\Iblock                 unsetDescription()
     * @method \string                               fillDescription()
     * @method \string                               getDescriptionType()
     * @method \Bitrix\Iblock\Iblock                 setDescriptionType(\Bitrix\Main\DB\SqlExpression|\string $descriptionType)
     * @method bool                                  hasDescriptionType()
     * @method bool                                  isDescriptionTypeFilled()
     * @method bool                                  isDescriptionTypeChanged()
     * @method \string                               remindActualDescriptionType()
     * @method \string                               requireDescriptionType()
     * @method \Bitrix\Iblock\Iblock                 resetDescriptionType()
     * @method \Bitrix\Iblock\Iblock                 unsetDescriptionType()
     * @method \string                               fillDescriptionType()
     * @method \string                               getXmlId()
     * @method \Bitrix\Iblock\Iblock                 setXmlId(\Bitrix\Main\DB\SqlExpression|\string $xmlId)
     * @method bool                                  hasXmlId()
     * @method bool                                  isXmlIdFilled()
     * @method bool                                  isXmlIdChanged()
     * @method \string                               remindActualXmlId()
     * @method \string                               requireXmlId()
     * @method \Bitrix\Iblock\Iblock                 resetXmlId()
     * @method \Bitrix\Iblock\Iblock                 unsetXmlId()
     * @method \string                               fillXmlId()
     * @method \string                               getTmpId()
     * @method \Bitrix\Iblock\Iblock                 setTmpId(\Bitrix\Main\DB\SqlExpression|\string $tmpId)
     * @method bool                                  hasTmpId()
     * @method bool                                  isTmpIdFilled()
     * @method bool                                  isTmpIdChanged()
     * @method \string                               remindActualTmpId()
     * @method \string                               requireTmpId()
     * @method \Bitrix\Iblock\Iblock                 resetTmpId()
     * @method \Bitrix\Iblock\Iblock                 unsetTmpId()
     * @method \string                               fillTmpId()
     * @method \boolean                              getIndexElement()
     * @method \Bitrix\Iblock\Iblock                 setIndexElement(\Bitrix\Main\DB\SqlExpression|\boolean $indexElement)
     * @method bool                                  hasIndexElement()
     * @method bool                                  isIndexElementFilled()
     * @method bool                                  isIndexElementChanged()
     * @method \boolean                              remindActualIndexElement()
     * @method \boolean                              requireIndexElement()
     * @method \Bitrix\Iblock\Iblock                 resetIndexElement()
     * @method \Bitrix\Iblock\Iblock                 unsetIndexElement()
     * @method \boolean                              fillIndexElement()
     * @method \boolean                              getIndexSection()
     * @method \Bitrix\Iblock\Iblock                 setIndexSection(\Bitrix\Main\DB\SqlExpression|\boolean $indexSection)
     * @method bool                                  hasIndexSection()
     * @method bool                                  isIndexSectionFilled()
     * @method bool                                  isIndexSectionChanged()
     * @method \boolean                              remindActualIndexSection()
     * @method \boolean                              requireIndexSection()
     * @method \Bitrix\Iblock\Iblock                 resetIndexSection()
     * @method \Bitrix\Iblock\Iblock                 unsetIndexSection()
     * @method \boolean                              fillIndexSection()
     * @method \boolean                              getWorkflow()
     * @method \Bitrix\Iblock\Iblock                 setWorkflow(\Bitrix\Main\DB\SqlExpression|\boolean $workflow)
     * @method bool                                  hasWorkflow()
     * @method bool                                  isWorkflowFilled()
     * @method bool                                  isWorkflowChanged()
     * @method \boolean                              remindActualWorkflow()
     * @method \boolean                              requireWorkflow()
     * @method \Bitrix\Iblock\Iblock                 resetWorkflow()
     * @method \Bitrix\Iblock\Iblock                 unsetWorkflow()
     * @method \boolean                              fillWorkflow()
     * @method \boolean                              getBizproc()
     * @method \Bitrix\Iblock\Iblock                 setBizproc(\Bitrix\Main\DB\SqlExpression|\boolean $bizproc)
     * @method bool                                  hasBizproc()
     * @method bool                                  isBizprocFilled()
     * @method bool                                  isBizprocChanged()
     * @method \boolean                              remindActualBizproc()
     * @method \boolean                              requireBizproc()
     * @method \Bitrix\Iblock\Iblock                 resetBizproc()
     * @method \Bitrix\Iblock\Iblock                 unsetBizproc()
     * @method \boolean                              fillBizproc()
     * @method \string                               getSectionChooser()
     * @method \Bitrix\Iblock\Iblock                 setSectionChooser(\Bitrix\Main\DB\SqlExpression|\string $sectionChooser)
     * @method bool                                  hasSectionChooser()
     * @method bool                                  isSectionChooserFilled()
     * @method bool                                  isSectionChooserChanged()
     * @method \string                               remindActualSectionChooser()
     * @method \string                               requireSectionChooser()
     * @method \Bitrix\Iblock\Iblock                 resetSectionChooser()
     * @method \Bitrix\Iblock\Iblock                 unsetSectionChooser()
     * @method \string                               fillSectionChooser()
     * @method \string                               getListMode()
     * @method \Bitrix\Iblock\Iblock                 setListMode(\Bitrix\Main\DB\SqlExpression|\string $listMode)
     * @method bool                                  hasListMode()
     * @method bool                                  isListModeFilled()
     * @method bool                                  isListModeChanged()
     * @method \string                               remindActualListMode()
     * @method \string                               requireListMode()
     * @method \Bitrix\Iblock\Iblock                 resetListMode()
     * @method \Bitrix\Iblock\Iblock                 unsetListMode()
     * @method \string                               fillListMode()
     * @method \string                               getRightsMode()
     * @method \Bitrix\Iblock\Iblock                 setRightsMode(\Bitrix\Main\DB\SqlExpression|\string $rightsMode)
     * @method bool                                  hasRightsMode()
     * @method bool                                  isRightsModeFilled()
     * @method bool                                  isRightsModeChanged()
     * @method \string                               remindActualRightsMode()
     * @method \string                               requireRightsMode()
     * @method \Bitrix\Iblock\Iblock                 resetRightsMode()
     * @method \Bitrix\Iblock\Iblock                 unsetRightsMode()
     * @method \string                               fillRightsMode()
     * @method \boolean                              getSectionProperty()
     * @method \Bitrix\Iblock\Iblock                 setSectionProperty(\Bitrix\Main\DB\SqlExpression|\boolean $sectionProperty)
     * @method bool                                  hasSectionProperty()
     * @method bool                                  isSectionPropertyFilled()
     * @method bool                                  isSectionPropertyChanged()
     * @method \boolean                              remindActualSectionProperty()
     * @method \boolean                              requireSectionProperty()
     * @method \Bitrix\Iblock\Iblock                 resetSectionProperty()
     * @method \Bitrix\Iblock\Iblock                 unsetSectionProperty()
     * @method \boolean                              fillSectionProperty()
     * @method \string                               getPropertyIndex()
     * @method \Bitrix\Iblock\Iblock                 setPropertyIndex(\Bitrix\Main\DB\SqlExpression|\string $propertyIndex)
     * @method bool                                  hasPropertyIndex()
     * @method bool                                  isPropertyIndexFilled()
     * @method bool                                  isPropertyIndexChanged()
     * @method \string                               remindActualPropertyIndex()
     * @method \string                               requirePropertyIndex()
     * @method \Bitrix\Iblock\Iblock                 resetPropertyIndex()
     * @method \Bitrix\Iblock\Iblock                 unsetPropertyIndex()
     * @method \string                               fillPropertyIndex()
     * @method \string                               getVersion()
     * @method \Bitrix\Iblock\Iblock                 setVersion(\Bitrix\Main\DB\SqlExpression|\string $version)
     * @method bool                                  hasVersion()
     * @method bool                                  isVersionFilled()
     * @method bool                                  isVersionChanged()
     * @method \string                               remindActualVersion()
     * @method \string                               requireVersion()
     * @method \Bitrix\Iblock\Iblock                 resetVersion()
     * @method \Bitrix\Iblock\Iblock                 unsetVersion()
     * @method \string                               fillVersion()
     * @method \int                                  getLastConvElement()
     * @method \Bitrix\Iblock\Iblock                 setLastConvElement(\Bitrix\Main\DB\SqlExpression|\int $lastConvElement)
     * @method bool                                  hasLastConvElement()
     * @method bool                                  isLastConvElementFilled()
     * @method bool                                  isLastConvElementChanged()
     * @method \int                                  remindActualLastConvElement()
     * @method \int                                  requireLastConvElement()
     * @method \Bitrix\Iblock\Iblock                 resetLastConvElement()
     * @method \Bitrix\Iblock\Iblock                 unsetLastConvElement()
     * @method \int                                  fillLastConvElement()
     * @method \int                                  getSocnetGroupId()
     * @method \Bitrix\Iblock\Iblock                 setSocnetGroupId(\Bitrix\Main\DB\SqlExpression|\int $socnetGroupId)
     * @method bool                                  hasSocnetGroupId()
     * @method bool                                  isSocnetGroupIdFilled()
     * @method bool                                  isSocnetGroupIdChanged()
     * @method \int                                  remindActualSocnetGroupId()
     * @method \int                                  requireSocnetGroupId()
     * @method \Bitrix\Iblock\Iblock                 resetSocnetGroupId()
     * @method \Bitrix\Iblock\Iblock                 unsetSocnetGroupId()
     * @method \int                                  fillSocnetGroupId()
     * @method \string                               getEditFileBefore()
     * @method \Bitrix\Iblock\Iblock                 setEditFileBefore(\Bitrix\Main\DB\SqlExpression|\string $editFileBefore)
     * @method bool                                  hasEditFileBefore()
     * @method bool                                  isEditFileBeforeFilled()
     * @method bool                                  isEditFileBeforeChanged()
     * @method \string                               remindActualEditFileBefore()
     * @method \string                               requireEditFileBefore()
     * @method \Bitrix\Iblock\Iblock                 resetEditFileBefore()
     * @method \Bitrix\Iblock\Iblock                 unsetEditFileBefore()
     * @method \string                               fillEditFileBefore()
     * @method \string                               getEditFileAfter()
     * @method \Bitrix\Iblock\Iblock                 setEditFileAfter(\Bitrix\Main\DB\SqlExpression|\string $editFileAfter)
     * @method bool                                  hasEditFileAfter()
     * @method bool                                  isEditFileAfterFilled()
     * @method bool                                  isEditFileAfterChanged()
     * @method \string                               remindActualEditFileAfter()
     * @method \string                               requireEditFileAfter()
     * @method \Bitrix\Iblock\Iblock                 resetEditFileAfter()
     * @method \Bitrix\Iblock\Iblock                 unsetEditFileAfter()
     * @method \string                               fillEditFileAfter()
     * @method \Bitrix\Iblock\EO_Type                getType()
     * @method \Bitrix\Iblock\EO_Type                remindActualType()
     * @method \Bitrix\Iblock\EO_Type                requireType()
     * @method \Bitrix\Iblock\Iblock                 setType(\Bitrix\Iblock\EO_Type $object)
     * @method \Bitrix\Iblock\Iblock                 resetType()
     * @method \Bitrix\Iblock\Iblock                 unsetType()
     * @method bool                                  hasType()
     * @method bool                                  isTypeFilled()
     * @method bool                                  isTypeChanged()
     * @method \Bitrix\Iblock\EO_Type                fillType()
     * @method \boolean                              getFulltextIndex()
     * @method \Bitrix\Iblock\Iblock                 setFulltextIndex(\Bitrix\Main\DB\SqlExpression|\boolean $fulltextIndex)
     * @method bool                                  hasFulltextIndex()
     * @method bool                                  isFulltextIndexFilled()
     * @method bool                                  isFulltextIndexChanged()
     * @method \boolean                              remindActualFulltextIndex()
     * @method \boolean                              requireFulltextIndex()
     * @method \Bitrix\Iblock\Iblock                 resetFulltextIndex()
     * @method \Bitrix\Iblock\Iblock                 unsetFulltextIndex()
     * @method \boolean                              fillFulltextIndex()
     * @method \Bitrix\Iblock\EO_Property_Collection getProperties()
     * @method \Bitrix\Iblock\EO_Property_Collection requireProperties()
     * @method \Bitrix\Iblock\EO_Property_Collection fillProperties()
     * @method bool                                  hasProperties()
     * @method bool                                  isPropertiesFilled()
     * @method bool                                  isPropertiesChanged()
     * @method void                                  addToProperties(\Bitrix\Iblock\Property $property)
     * @method void                                  removeFromProperties(\Bitrix\Iblock\Property $property)
     * @method void                                  removeAllProperties()
     * @method \Bitrix\Iblock\Iblock                 resetProperties()
     * @method \Bitrix\Iblock\Iblock                 unsetProperties()
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
     * @method        \Bitrix\Iblock\Iblock                                                                           set($fieldName, $value)
     * @method        \Bitrix\Iblock\Iblock                                                                           reset($fieldName)
     * @method        \Bitrix\Iblock\Iblock                                                                           unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Bitrix\Iblock\Iblock                                                                           wakeUp($data)
     */
    class EO_Iblock
    {
        // @var \Bitrix\Iblock\IblockTable
        public static $dataClass = '\Bitrix\Iblock\IblockTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Bitrix\Iblock {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_Iblock_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                                  getIdList()
     * @method \Bitrix\Main\Type\DateTime[]            getTimestampXList()
     * @method \Bitrix\Main\Type\DateTime[]            fillTimestampX()
     * @method \string[]                               getIblockTypeIdList()
     * @method \string[]                               fillIblockTypeId()
     * @method \string[]                               getLidList()
     * @method \string[]                               fillLid()
     * @method \string[]                               getCodeList()
     * @method \string[]                               fillCode()
     * @method \string[]                               getApiCodeList()
     * @method \string[]                               fillApiCode()
     * @method \boolean[]                              getRestOnList()
     * @method \boolean[]                              fillRestOn()
     * @method \string[]                               getNameList()
     * @method \string[]                               fillName()
     * @method \boolean[]                              getActiveList()
     * @method \boolean[]                              fillActive()
     * @method \int[]                                  getSortList()
     * @method \int[]                                  fillSort()
     * @method \string[]                               getListPageUrlList()
     * @method \string[]                               fillListPageUrl()
     * @method \string[]                               getDetailPageUrlList()
     * @method \string[]                               fillDetailPageUrl()
     * @method \string[]                               getSectionPageUrlList()
     * @method \string[]                               fillSectionPageUrl()
     * @method \string[]                               getCanonicalPageUrlList()
     * @method \string[]                               fillCanonicalPageUrl()
     * @method \int[]                                  getPictureList()
     * @method \int[]                                  fillPicture()
     * @method \string[]                               getDescriptionList()
     * @method \string[]                               fillDescription()
     * @method \string[]                               getDescriptionTypeList()
     * @method \string[]                               fillDescriptionType()
     * @method \string[]                               getXmlIdList()
     * @method \string[]                               fillXmlId()
     * @method \string[]                               getTmpIdList()
     * @method \string[]                               fillTmpId()
     * @method \boolean[]                              getIndexElementList()
     * @method \boolean[]                              fillIndexElement()
     * @method \boolean[]                              getIndexSectionList()
     * @method \boolean[]                              fillIndexSection()
     * @method \boolean[]                              getWorkflowList()
     * @method \boolean[]                              fillWorkflow()
     * @method \boolean[]                              getBizprocList()
     * @method \boolean[]                              fillBizproc()
     * @method \string[]                               getSectionChooserList()
     * @method \string[]                               fillSectionChooser()
     * @method \string[]                               getListModeList()
     * @method \string[]                               fillListMode()
     * @method \string[]                               getRightsModeList()
     * @method \string[]                               fillRightsMode()
     * @method \boolean[]                              getSectionPropertyList()
     * @method \boolean[]                              fillSectionProperty()
     * @method \string[]                               getPropertyIndexList()
     * @method \string[]                               fillPropertyIndex()
     * @method \string[]                               getVersionList()
     * @method \string[]                               fillVersion()
     * @method \int[]                                  getLastConvElementList()
     * @method \int[]                                  fillLastConvElement()
     * @method \int[]                                  getSocnetGroupIdList()
     * @method \int[]                                  fillSocnetGroupId()
     * @method \string[]                               getEditFileBeforeList()
     * @method \string[]                               fillEditFileBefore()
     * @method \string[]                               getEditFileAfterList()
     * @method \string[]                               fillEditFileAfter()
     * @method \Bitrix\Iblock\EO_Type[]                getTypeList()
     * @method \Bitrix\Iblock\EO_Iblock_Collection     getTypeCollection()
     * @method \Bitrix\Iblock\EO_Type_Collection       fillType()
     * @method \boolean[]                              getFulltextIndexList()
     * @method \boolean[]                              fillFulltextIndex()
     * @method \Bitrix\Iblock\EO_Property_Collection[] getPropertiesList()
     * @method \Bitrix\Iblock\EO_Property_Collection   getPropertiesCollection()
     * @method \Bitrix\Iblock\EO_Property_Collection   fillProperties()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\Bitrix\Iblock\Iblock $object)
     * @method        bool                                             has(\Bitrix\Iblock\Iblock $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \Bitrix\Iblock\Iblock                            getByPrimary($primary)
     * @method        \Bitrix\Iblock\Iblock[]                          getAll()
     * @method        bool                                             remove(\Bitrix\Iblock\Iblock $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Bitrix\Iblock\EO_Iblock_Collection              wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \Bitrix\Iblock\Iblock                            current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \Bitrix\Iblock\EO_Iblock_Collection              merge(?\Bitrix\Iblock\EO_Iblock_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_Iblock_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Bitrix\Iblock\IblockTable
        public static $dataClass = '\Bitrix\Iblock\IblockTable';
    }
}

namespace Bitrix\Iblock {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_Iblock_Query                     query()
     * @method static EO_Iblock_Result                    getByPrimary($primary, array $parameters = [])
     * @method static EO_Iblock_Result                    getById($id)
     * @method static EO_Iblock_Result                    getList(array $parameters = [])
     * @method static EO_Iblock_Entity                    getEntity()
     * @method static \Bitrix\Iblock\Iblock               createObject($setDefaultValues = true)
     * @method static \Bitrix\Iblock\EO_Iblock_Collection createCollection()
     * @method static \Bitrix\Iblock\Iblock               wakeUpObject($row)
     * @method static \Bitrix\Iblock\EO_Iblock_Collection wakeUpCollection($rows)
     */
    class IblockTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_Iblock_Result                    exec()
     * @method \Bitrix\Iblock\Iblock               fetchObject()
     * @method \Bitrix\Iblock\EO_Iblock_Collection fetchCollection()
     */
    class EO_Iblock_Query extends Query {}
    /**
     * @method \Bitrix\Iblock\Iblock               fetchObject()
     * @method \Bitrix\Iblock\EO_Iblock_Collection fetchCollection()
     */
    class EO_Iblock_Result extends Result {}
    /**
     * @method \Bitrix\Iblock\Iblock               createObject($setDefaultValues = true)
     * @method \Bitrix\Iblock\EO_Iblock_Collection createCollection()
     * @method \Bitrix\Iblock\Iblock               wakeUpObject($row)
     * @method \Bitrix\Iblock\EO_Iblock_Collection wakeUpCollection($rows)
     */
    class EO_Iblock_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitCurrencyTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitCurrency
     *
     * @see RebitCurrencyTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int              getId()
     * @method \EO_RebitCurrency setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool              hasId()
     * @method bool              isIdFilled()
     * @method bool              isIdChanged()
     * @method null|\string      getUfCode()
     * @method \EO_RebitCurrency setUfCode(null|\Bitrix\Main\DB\SqlExpression|\string $ufCode)
     * @method bool              hasUfCode()
     * @method bool              isUfCodeFilled()
     * @method bool              isUfCodeChanged()
     * @method null|\string      remindActualUfCode()
     * @method null|\string      requireUfCode()
     * @method \EO_RebitCurrency resetUfCode()
     * @method \EO_RebitCurrency unsetUfCode()
     * @method null|\string      fillUfCode()
     * @method null|\string      getUfName()
     * @method \EO_RebitCurrency setUfName(null|\Bitrix\Main\DB\SqlExpression|\string $ufName)
     * @method bool              hasUfName()
     * @method bool              isUfNameFilled()
     * @method bool              isUfNameChanged()
     * @method null|\string      remindActualUfName()
     * @method null|\string      requireUfName()
     * @method \EO_RebitCurrency resetUfName()
     * @method \EO_RebitCurrency unsetUfName()
     * @method null|\string      fillUfName()
     * @method null|\string      getUfType()
     * @method \EO_RebitCurrency setUfType(null|\Bitrix\Main\DB\SqlExpression|\string $ufType)
     * @method bool              hasUfType()
     * @method bool              isUfTypeFilled()
     * @method bool              isUfTypeChanged()
     * @method null|\string      remindActualUfType()
     * @method null|\string      requireUfType()
     * @method \EO_RebitCurrency resetUfType()
     * @method \EO_RebitCurrency unsetUfType()
     * @method null|\string      fillUfType()
     * @method null|\int         getUfDecimals()
     * @method \EO_RebitCurrency setUfDecimals(null|\Bitrix\Main\DB\SqlExpression|\int $ufDecimals)
     * @method bool              hasUfDecimals()
     * @method bool              isUfDecimalsFilled()
     * @method bool              isUfDecimalsChanged()
     * @method null|\int         remindActualUfDecimals()
     * @method null|\int         requireUfDecimals()
     * @method \EO_RebitCurrency resetUfDecimals()
     * @method \EO_RebitCurrency unsetUfDecimals()
     * @method null|\int         fillUfDecimals()
     * @method null|\int         getUfIcon()
     * @method \EO_RebitCurrency setUfIcon(null|\Bitrix\Main\DB\SqlExpression|\int $ufIcon)
     * @method bool              hasUfIcon()
     * @method bool              isUfIconFilled()
     * @method bool              isUfIconChanged()
     * @method null|\int         remindActualUfIcon()
     * @method null|\int         requireUfIcon()
     * @method \EO_RebitCurrency resetUfIcon()
     * @method \EO_RebitCurrency unsetUfIcon()
     * @method null|\int         fillUfIcon()
     * @method null|\boolean     getUfIsActive()
     * @method \EO_RebitCurrency setUfIsActive(null|\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool              hasUfIsActive()
     * @method bool              isUfIsActiveFilled()
     * @method bool              isUfIsActiveChanged()
     * @method null|\boolean     remindActualUfIsActive()
     * @method null|\boolean     requireUfIsActive()
     * @method \EO_RebitCurrency resetUfIsActive()
     * @method \EO_RebitCurrency unsetUfIsActive()
     * @method null|\boolean     fillUfIsActive()
     * @method null|\int         getUfSort()
     * @method \EO_RebitCurrency setUfSort(null|\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool              hasUfSort()
     * @method bool              isUfSortFilled()
     * @method bool              isUfSortChanged()
     * @method null|\int         remindActualUfSort()
     * @method null|\int         requireUfSort()
     * @method \EO_RebitCurrency resetUfSort()
     * @method \EO_RebitCurrency unsetUfSort()
     * @method null|\int         fillUfSort()
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
     * @method        \EO_RebitCurrency                                                                               set($fieldName, $value)
     * @method        \EO_RebitCurrency                                                                               reset($fieldName)
     * @method        \EO_RebitCurrency                                                                               unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitCurrency                                                                               wakeUp($data)
     */
    class EO_RebitCurrency
    {
        // @var \RebitCurrencyTable
        public static $dataClass = '\RebitCurrencyTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitCurrency_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]          getIdList()
     * @method null|\string[]  getUfCodeList()
     * @method null|\string[]  fillUfCode()
     * @method null|\string[]  getUfNameList()
     * @method null|\string[]  fillUfName()
     * @method null|\string[]  getUfTypeList()
     * @method null|\string[]  fillUfType()
     * @method null|\int[]     getUfDecimalsList()
     * @method null|\int[]     fillUfDecimals()
     * @method null|\int[]     getUfIconList()
     * @method null|\int[]     fillUfIcon()
     * @method null|\boolean[] getUfIsActiveList()
     * @method null|\boolean[] fillUfIsActive()
     * @method null|\int[]     getUfSortList()
     * @method null|\int[]     fillUfSort()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitCurrency $object)
     * @method        bool                                             has(\EO_RebitCurrency $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitCurrency                                getByPrimary($primary)
     * @method        \EO_RebitCurrency[]                              getAll()
     * @method        bool                                             remove(\EO_RebitCurrency $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitCurrency_Collection                     wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitCurrency                                current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitCurrency_Collection                     merge(?\EO_RebitCurrency_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitCurrency_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitCurrencyTable
        public static $dataClass = '\RebitCurrencyTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitCurrency_Query       query()
     * @method static EO_RebitCurrency_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitCurrency_Result      getById($id)
     * @method static EO_RebitCurrency_Result      getList(array $parameters = [])
     * @method static EO_RebitCurrency_Entity      getEntity()
     * @method static \EO_RebitCurrency            createObject($setDefaultValues = true)
     * @method static \EO_RebitCurrency_Collection createCollection()
     * @method static \EO_RebitCurrency            wakeUpObject($row)
     * @method static \EO_RebitCurrency_Collection wakeUpCollection($rows)
     */
    class RebitCurrencyTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitCurrency_Result      exec()
     * @method \EO_RebitCurrency            fetchObject()
     * @method \EO_RebitCurrency_Collection fetchCollection()
     */
    class EO_RebitCurrency_Query extends Query {}
    /**
     * @method \EO_RebitCurrency            fetchObject()
     * @method \EO_RebitCurrency_Collection fetchCollection()
     */
    class EO_RebitCurrency_Result extends Result {}
    /**
     * @method \EO_RebitCurrency            createObject($setDefaultValues = true)
     * @method \EO_RebitCurrency_Collection createCollection()
     * @method \EO_RebitCurrency            wakeUpObject($row)
     * @method \EO_RebitCurrency_Collection wakeUpCollection($rows)
     */
    class EO_RebitCurrency_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitCurrencyPairTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitCurrencyPair
     *
     * @see RebitCurrencyPairTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                  getId()
     * @method \EO_RebitCurrencyPair setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                  hasId()
     * @method bool                  isIdFilled()
     * @method bool                  isIdChanged()
     * @method null|\int             getUfTokenCurrencyId()
     * @method \EO_RebitCurrencyPair setUfTokenCurrencyId(null|\Bitrix\Main\DB\SqlExpression|\int $ufTokenCurrencyId)
     * @method bool                  hasUfTokenCurrencyId()
     * @method bool                  isUfTokenCurrencyIdFilled()
     * @method bool                  isUfTokenCurrencyIdChanged()
     * @method null|\int             remindActualUfTokenCurrencyId()
     * @method null|\int             requireUfTokenCurrencyId()
     * @method \EO_RebitCurrencyPair resetUfTokenCurrencyId()
     * @method \EO_RebitCurrencyPair unsetUfTokenCurrencyId()
     * @method null|\int             fillUfTokenCurrencyId()
     * @method null|\int             getUfFiatCurrencyId()
     * @method \EO_RebitCurrencyPair setUfFiatCurrencyId(null|\Bitrix\Main\DB\SqlExpression|\int $ufFiatCurrencyId)
     * @method bool                  hasUfFiatCurrencyId()
     * @method bool                  isUfFiatCurrencyIdFilled()
     * @method bool                  isUfFiatCurrencyIdChanged()
     * @method null|\int             remindActualUfFiatCurrencyId()
     * @method null|\int             requireUfFiatCurrencyId()
     * @method \EO_RebitCurrencyPair resetUfFiatCurrencyId()
     * @method \EO_RebitCurrencyPair unsetUfFiatCurrencyId()
     * @method null|\int             fillUfFiatCurrencyId()
     * @method null|\string          getUfCode()
     * @method \EO_RebitCurrencyPair setUfCode(null|\Bitrix\Main\DB\SqlExpression|\string $ufCode)
     * @method bool                  hasUfCode()
     * @method bool                  isUfCodeFilled()
     * @method bool                  isUfCodeChanged()
     * @method null|\string          remindActualUfCode()
     * @method null|\string          requireUfCode()
     * @method \EO_RebitCurrencyPair resetUfCode()
     * @method \EO_RebitCurrencyPair unsetUfCode()
     * @method null|\string          fillUfCode()
     * @method null|\boolean         getUfIsActive()
     * @method \EO_RebitCurrencyPair setUfIsActive(null|\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                  hasUfIsActive()
     * @method bool                  isUfIsActiveFilled()
     * @method bool                  isUfIsActiveChanged()
     * @method null|\boolean         remindActualUfIsActive()
     * @method null|\boolean         requireUfIsActive()
     * @method \EO_RebitCurrencyPair resetUfIsActive()
     * @method \EO_RebitCurrencyPair unsetUfIsActive()
     * @method null|\boolean         fillUfIsActive()
     * @method null|\boolean         getUfIsDefault()
     * @method \EO_RebitCurrencyPair setUfIsDefault(null|\Bitrix\Main\DB\SqlExpression|\boolean $ufIsDefault)
     * @method bool                  hasUfIsDefault()
     * @method bool                  isUfIsDefaultFilled()
     * @method bool                  isUfIsDefaultChanged()
     * @method null|\boolean         remindActualUfIsDefault()
     * @method null|\boolean         requireUfIsDefault()
     * @method \EO_RebitCurrencyPair resetUfIsDefault()
     * @method \EO_RebitCurrencyPair unsetUfIsDefault()
     * @method null|\boolean         fillUfIsDefault()
     * @method null|\int             getUfSort()
     * @method \EO_RebitCurrencyPair setUfSort(null|\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                  hasUfSort()
     * @method bool                  isUfSortFilled()
     * @method bool                  isUfSortChanged()
     * @method null|\int             remindActualUfSort()
     * @method null|\int             requireUfSort()
     * @method \EO_RebitCurrencyPair resetUfSort()
     * @method \EO_RebitCurrencyPair unsetUfSort()
     * @method null|\int             fillUfSort()
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
     * @method        \EO_RebitCurrencyPair                                                                           set($fieldName, $value)
     * @method        \EO_RebitCurrencyPair                                                                           reset($fieldName)
     * @method        \EO_RebitCurrencyPair                                                                           unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitCurrencyPair                                                                           wakeUp($data)
     */
    class EO_RebitCurrencyPair
    {
        // @var \RebitCurrencyPairTable
        public static $dataClass = '\RebitCurrencyPairTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitCurrencyPair_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]          getIdList()
     * @method null|\int[]     getUfTokenCurrencyIdList()
     * @method null|\int[]     fillUfTokenCurrencyId()
     * @method null|\int[]     getUfFiatCurrencyIdList()
     * @method null|\int[]     fillUfFiatCurrencyId()
     * @method null|\string[]  getUfCodeList()
     * @method null|\string[]  fillUfCode()
     * @method null|\boolean[] getUfIsActiveList()
     * @method null|\boolean[] fillUfIsActive()
     * @method null|\boolean[] getUfIsDefaultList()
     * @method null|\boolean[] fillUfIsDefault()
     * @method null|\int[]     getUfSortList()
     * @method null|\int[]     fillUfSort()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitCurrencyPair $object)
     * @method        bool                                             has(\EO_RebitCurrencyPair $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitCurrencyPair                            getByPrimary($primary)
     * @method        \EO_RebitCurrencyPair[]                          getAll()
     * @method        bool                                             remove(\EO_RebitCurrencyPair $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitCurrencyPair_Collection                 wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitCurrencyPair                            current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitCurrencyPair_Collection                 merge(?\EO_RebitCurrencyPair_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitCurrencyPair_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitCurrencyPairTable
        public static $dataClass = '\RebitCurrencyPairTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitCurrencyPair_Query       query()
     * @method static EO_RebitCurrencyPair_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitCurrencyPair_Result      getById($id)
     * @method static EO_RebitCurrencyPair_Result      getList(array $parameters = [])
     * @method static EO_RebitCurrencyPair_Entity      getEntity()
     * @method static \EO_RebitCurrencyPair            createObject($setDefaultValues = true)
     * @method static \EO_RebitCurrencyPair_Collection createCollection()
     * @method static \EO_RebitCurrencyPair            wakeUpObject($row)
     * @method static \EO_RebitCurrencyPair_Collection wakeUpCollection($rows)
     */
    class RebitCurrencyPairTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitCurrencyPair_Result      exec()
     * @method \EO_RebitCurrencyPair            fetchObject()
     * @method \EO_RebitCurrencyPair_Collection fetchCollection()
     */
    class EO_RebitCurrencyPair_Query extends Query {}
    /**
     * @method \EO_RebitCurrencyPair            fetchObject()
     * @method \EO_RebitCurrencyPair_Collection fetchCollection()
     */
    class EO_RebitCurrencyPair_Result extends Result {}
    /**
     * @method \EO_RebitCurrencyPair            createObject($setDefaultValues = true)
     * @method \EO_RebitCurrencyPair_Collection createCollection()
     * @method \EO_RebitCurrencyPair            wakeUpObject($row)
     * @method \EO_RebitCurrencyPair_Collection wakeUpCollection($rows)
     */
    class EO_RebitCurrencyPair_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitPaymentMethodTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitPaymentMethod
     *
     * @see RebitPaymentMethodTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                   getId()
     * @method \EO_RebitPaymentMethod setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                   hasId()
     * @method bool                   isIdFilled()
     * @method bool                   isIdChanged()
     * @method null|\string           getUfCode()
     * @method \EO_RebitPaymentMethod setUfCode(null|\Bitrix\Main\DB\SqlExpression|\string $ufCode)
     * @method bool                   hasUfCode()
     * @method bool                   isUfCodeFilled()
     * @method bool                   isUfCodeChanged()
     * @method null|\string           remindActualUfCode()
     * @method null|\string           requireUfCode()
     * @method \EO_RebitPaymentMethod resetUfCode()
     * @method \EO_RebitPaymentMethod unsetUfCode()
     * @method null|\string           fillUfCode()
     * @method null|\string           getUfName()
     * @method \EO_RebitPaymentMethod setUfName(null|\Bitrix\Main\DB\SqlExpression|\string $ufName)
     * @method bool                   hasUfName()
     * @method bool                   isUfNameFilled()
     * @method bool                   isUfNameChanged()
     * @method null|\string           remindActualUfName()
     * @method null|\string           requireUfName()
     * @method \EO_RebitPaymentMethod resetUfName()
     * @method \EO_RebitPaymentMethod unsetUfName()
     * @method null|\string           fillUfName()
     * @method null|\int              getUfIcon()
     * @method \EO_RebitPaymentMethod setUfIcon(null|\Bitrix\Main\DB\SqlExpression|\int $ufIcon)
     * @method bool                   hasUfIcon()
     * @method bool                   isUfIconFilled()
     * @method bool                   isUfIconChanged()
     * @method null|\int              remindActualUfIcon()
     * @method null|\int              requireUfIcon()
     * @method \EO_RebitPaymentMethod resetUfIcon()
     * @method \EO_RebitPaymentMethod unsetUfIcon()
     * @method null|\int              fillUfIcon()
     * @method null|\boolean          getUfIsActive()
     * @method \EO_RebitPaymentMethod setUfIsActive(null|\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                   hasUfIsActive()
     * @method bool                   isUfIsActiveFilled()
     * @method bool                   isUfIsActiveChanged()
     * @method null|\boolean          remindActualUfIsActive()
     * @method null|\boolean          requireUfIsActive()
     * @method \EO_RebitPaymentMethod resetUfIsActive()
     * @method \EO_RebitPaymentMethod unsetUfIsActive()
     * @method null|\boolean          fillUfIsActive()
     * @method null|\int              getUfSort()
     * @method \EO_RebitPaymentMethod setUfSort(null|\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                   hasUfSort()
     * @method bool                   isUfSortFilled()
     * @method bool                   isUfSortChanged()
     * @method null|\int              remindActualUfSort()
     * @method null|\int              requireUfSort()
     * @method \EO_RebitPaymentMethod resetUfSort()
     * @method \EO_RebitPaymentMethod unsetUfSort()
     * @method null|\int              fillUfSort()
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
     * @method        \EO_RebitPaymentMethod                                                                          set($fieldName, $value)
     * @method        \EO_RebitPaymentMethod                                                                          reset($fieldName)
     * @method        \EO_RebitPaymentMethod                                                                          unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitPaymentMethod                                                                          wakeUp($data)
     */
    class EO_RebitPaymentMethod
    {
        // @var \RebitPaymentMethodTable
        public static $dataClass = '\RebitPaymentMethodTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitPaymentMethod_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]          getIdList()
     * @method null|\string[]  getUfCodeList()
     * @method null|\string[]  fillUfCode()
     * @method null|\string[]  getUfNameList()
     * @method null|\string[]  fillUfName()
     * @method null|\int[]     getUfIconList()
     * @method null|\int[]     fillUfIcon()
     * @method null|\boolean[] getUfIsActiveList()
     * @method null|\boolean[] fillUfIsActive()
     * @method null|\int[]     getUfSortList()
     * @method null|\int[]     fillUfSort()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitPaymentMethod $object)
     * @method        bool                                             has(\EO_RebitPaymentMethod $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitPaymentMethod                           getByPrimary($primary)
     * @method        \EO_RebitPaymentMethod[]                         getAll()
     * @method        bool                                             remove(\EO_RebitPaymentMethod $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitPaymentMethod_Collection                wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitPaymentMethod                           current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitPaymentMethod_Collection                merge(?\EO_RebitPaymentMethod_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitPaymentMethod_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitPaymentMethodTable
        public static $dataClass = '\RebitPaymentMethodTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitPaymentMethod_Query       query()
     * @method static EO_RebitPaymentMethod_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitPaymentMethod_Result      getById($id)
     * @method static EO_RebitPaymentMethod_Result      getList(array $parameters = [])
     * @method static EO_RebitPaymentMethod_Entity      getEntity()
     * @method static \EO_RebitPaymentMethod            createObject($setDefaultValues = true)
     * @method static \EO_RebitPaymentMethod_Collection createCollection()
     * @method static \EO_RebitPaymentMethod            wakeUpObject($row)
     * @method static \EO_RebitPaymentMethod_Collection wakeUpCollection($rows)
     */
    class RebitPaymentMethodTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitPaymentMethod_Result      exec()
     * @method \EO_RebitPaymentMethod            fetchObject()
     * @method \EO_RebitPaymentMethod_Collection fetchCollection()
     */
    class EO_RebitPaymentMethod_Query extends Query {}
    /**
     * @method \EO_RebitPaymentMethod            fetchObject()
     * @method \EO_RebitPaymentMethod_Collection fetchCollection()
     */
    class EO_RebitPaymentMethod_Result extends Result {}
    /**
     * @method \EO_RebitPaymentMethod            createObject($setDefaultValues = true)
     * @method \EO_RebitPaymentMethod_Collection createCollection()
     * @method \EO_RebitPaymentMethod            wakeUpObject($row)
     * @method \EO_RebitPaymentMethod_Collection wakeUpCollection($rows)
     */
    class EO_RebitPaymentMethod_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitOrderBookTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitOrderBook
     *
     * @see RebitOrderBookTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitOrderBook              setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\string                    getUfBybitOrderId()
     * @method \EO_RebitOrderBook              setUfBybitOrderId(null|\Bitrix\Main\DB\SqlExpression|\string $ufBybitOrderId)
     * @method bool                            hasUfBybitOrderId()
     * @method bool                            isUfBybitOrderIdFilled()
     * @method bool                            isUfBybitOrderIdChanged()
     * @method null|\string                    remindActualUfBybitOrderId()
     * @method null|\string                    requireUfBybitOrderId()
     * @method \EO_RebitOrderBook              resetUfBybitOrderId()
     * @method \EO_RebitOrderBook              unsetUfBybitOrderId()
     * @method null|\string                    fillUfBybitOrderId()
     * @method null|\int                       getUfCurrencyPairId()
     * @method \EO_RebitOrderBook              setUfCurrencyPairId(null|\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyPairId)
     * @method bool                            hasUfCurrencyPairId()
     * @method bool                            isUfCurrencyPairIdFilled()
     * @method bool                            isUfCurrencyPairIdChanged()
     * @method null|\int                       remindActualUfCurrencyPairId()
     * @method null|\int                       requireUfCurrencyPairId()
     * @method \EO_RebitOrderBook              resetUfCurrencyPairId()
     * @method \EO_RebitOrderBook              unsetUfCurrencyPairId()
     * @method null|\int                       fillUfCurrencyPairId()
     * @method null|\string                    getUfSide()
     * @method \EO_RebitOrderBook              setUfSide(null|\Bitrix\Main\DB\SqlExpression|\string $ufSide)
     * @method bool                            hasUfSide()
     * @method bool                            isUfSideFilled()
     * @method bool                            isUfSideChanged()
     * @method null|\string                    remindActualUfSide()
     * @method null|\string                    requireUfSide()
     * @method \EO_RebitOrderBook              resetUfSide()
     * @method \EO_RebitOrderBook              unsetUfSide()
     * @method null|\string                    fillUfSide()
     * @method null|\float                     getUfPrice()
     * @method \EO_RebitOrderBook              setUfPrice(null|\Bitrix\Main\DB\SqlExpression|\float $ufPrice)
     * @method bool                            hasUfPrice()
     * @method bool                            isUfPriceFilled()
     * @method bool                            isUfPriceChanged()
     * @method null|\float                     remindActualUfPrice()
     * @method null|\float                     requireUfPrice()
     * @method \EO_RebitOrderBook              resetUfPrice()
     * @method \EO_RebitOrderBook              unsetUfPrice()
     * @method null|\float                     fillUfPrice()
     * @method null|\float                     getUfQuantity()
     * @method \EO_RebitOrderBook              setUfQuantity(null|\Bitrix\Main\DB\SqlExpression|\float $ufQuantity)
     * @method bool                            hasUfQuantity()
     * @method bool                            isUfQuantityFilled()
     * @method bool                            isUfQuantityChanged()
     * @method null|\float                     remindActualUfQuantity()
     * @method null|\float                     requireUfQuantity()
     * @method \EO_RebitOrderBook              resetUfQuantity()
     * @method \EO_RebitOrderBook              unsetUfQuantity()
     * @method null|\float                     fillUfQuantity()
     * @method null|\float                     getUfMinAmount()
     * @method \EO_RebitOrderBook              setUfMinAmount(null|\Bitrix\Main\DB\SqlExpression|\float $ufMinAmount)
     * @method bool                            hasUfMinAmount()
     * @method bool                            isUfMinAmountFilled()
     * @method bool                            isUfMinAmountChanged()
     * @method null|\float                     remindActualUfMinAmount()
     * @method null|\float                     requireUfMinAmount()
     * @method \EO_RebitOrderBook              resetUfMinAmount()
     * @method \EO_RebitOrderBook              unsetUfMinAmount()
     * @method null|\float                     fillUfMinAmount()
     * @method null|\float                     getUfMaxAmount()
     * @method \EO_RebitOrderBook              setUfMaxAmount(null|\Bitrix\Main\DB\SqlExpression|\float $ufMaxAmount)
     * @method bool                            hasUfMaxAmount()
     * @method bool                            isUfMaxAmountFilled()
     * @method bool                            isUfMaxAmountChanged()
     * @method null|\float                     remindActualUfMaxAmount()
     * @method null|\float                     requireUfMaxAmount()
     * @method \EO_RebitOrderBook              resetUfMaxAmount()
     * @method \EO_RebitOrderBook              unsetUfMaxAmount()
     * @method null|\float                     fillUfMaxAmount()
     * @method null|\string                    getUfCounterpartyName()
     * @method \EO_RebitOrderBook              setUfCounterpartyName(null|\Bitrix\Main\DB\SqlExpression|\string $ufCounterpartyName)
     * @method bool                            hasUfCounterpartyName()
     * @method bool                            isUfCounterpartyNameFilled()
     * @method bool                            isUfCounterpartyNameChanged()
     * @method null|\string                    remindActualUfCounterpartyName()
     * @method null|\string                    requireUfCounterpartyName()
     * @method \EO_RebitOrderBook              resetUfCounterpartyName()
     * @method \EO_RebitOrderBook              unsetUfCounterpartyName()
     * @method null|\string                    fillUfCounterpartyName()
     * @method null|\float                     getUfCounterpartyRating()
     * @method \EO_RebitOrderBook              setUfCounterpartyRating(null|\Bitrix\Main\DB\SqlExpression|\float $ufCounterpartyRating)
     * @method bool                            hasUfCounterpartyRating()
     * @method bool                            isUfCounterpartyRatingFilled()
     * @method bool                            isUfCounterpartyRatingChanged()
     * @method null|\float                     remindActualUfCounterpartyRating()
     * @method null|\float                     requireUfCounterpartyRating()
     * @method \EO_RebitOrderBook              resetUfCounterpartyRating()
     * @method \EO_RebitOrderBook              unsetUfCounterpartyRating()
     * @method null|\float                     fillUfCounterpartyRating()
     * @method null|\int                       getUfCounterpartyTrades()
     * @method \EO_RebitOrderBook              setUfCounterpartyTrades(null|\Bitrix\Main\DB\SqlExpression|\int $ufCounterpartyTrades)
     * @method bool                            hasUfCounterpartyTrades()
     * @method bool                            isUfCounterpartyTradesFilled()
     * @method bool                            isUfCounterpartyTradesChanged()
     * @method null|\int                       remindActualUfCounterpartyTrades()
     * @method null|\int                       requireUfCounterpartyTrades()
     * @method \EO_RebitOrderBook              resetUfCounterpartyTrades()
     * @method \EO_RebitOrderBook              unsetUfCounterpartyTrades()
     * @method null|\int                       fillUfCounterpartyTrades()
     * @method null|\float                     getUfCounterpartyCompletionRate()
     * @method \EO_RebitOrderBook              setUfCounterpartyCompletionRate(null|\Bitrix\Main\DB\SqlExpression|\float $ufCounterpartyCompletionRate)
     * @method bool                            hasUfCounterpartyCompletionRate()
     * @method bool                            isUfCounterpartyCompletionRateFilled()
     * @method bool                            isUfCounterpartyCompletionRateChanged()
     * @method null|\float                     remindActualUfCounterpartyCompletionRate()
     * @method null|\float                     requireUfCounterpartyCompletionRate()
     * @method \EO_RebitOrderBook              resetUfCounterpartyCompletionRate()
     * @method \EO_RebitOrderBook              unsetUfCounterpartyCompletionRate()
     * @method null|\float                     fillUfCounterpartyCompletionRate()
     * @method null|\string                    getUfPaymentMethodIds()
     * @method \EO_RebitOrderBook              setUfPaymentMethodIds(null|\Bitrix\Main\DB\SqlExpression|\string $ufPaymentMethodIds)
     * @method bool                            hasUfPaymentMethodIds()
     * @method bool                            isUfPaymentMethodIdsFilled()
     * @method bool                            isUfPaymentMethodIdsChanged()
     * @method null|\string                    remindActualUfPaymentMethodIds()
     * @method null|\string                    requireUfPaymentMethodIds()
     * @method \EO_RebitOrderBook              resetUfPaymentMethodIds()
     * @method \EO_RebitOrderBook              unsetUfPaymentMethodIds()
     * @method null|\string                    fillUfPaymentMethodIds()
     * @method null|\int                       getUfPaymentTimeLimit()
     * @method \EO_RebitOrderBook              setUfPaymentTimeLimit(null|\Bitrix\Main\DB\SqlExpression|\int $ufPaymentTimeLimit)
     * @method bool                            hasUfPaymentTimeLimit()
     * @method bool                            isUfPaymentTimeLimitFilled()
     * @method bool                            isUfPaymentTimeLimitChanged()
     * @method null|\int                       remindActualUfPaymentTimeLimit()
     * @method null|\int                       requireUfPaymentTimeLimit()
     * @method \EO_RebitOrderBook              resetUfPaymentTimeLimit()
     * @method \EO_RebitOrderBook              unsetUfPaymentTimeLimit()
     * @method null|\int                       fillUfPaymentTimeLimit()
     * @method null|\Bitrix\Main\Type\DateTime getUfSyncedAt()
     * @method \EO_RebitOrderBook              setUfSyncedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufSyncedAt)
     * @method bool                            hasUfSyncedAt()
     * @method bool                            isUfSyncedAtFilled()
     * @method bool                            isUfSyncedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfSyncedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfSyncedAt()
     * @method \EO_RebitOrderBook              resetUfSyncedAt()
     * @method \EO_RebitOrderBook              unsetUfSyncedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfSyncedAt()
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
     * @method        \EO_RebitOrderBook                                                                              set($fieldName, $value)
     * @method        \EO_RebitOrderBook                                                                              reset($fieldName)
     * @method        \EO_RebitOrderBook                                                                              unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitOrderBook                                                                              wakeUp($data)
     */
    class EO_RebitOrderBook
    {
        // @var \RebitOrderBookTable
        public static $dataClass = '\RebitOrderBookTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitOrderBook_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\string[]                    getUfBybitOrderIdList()
     * @method null|\string[]                    fillUfBybitOrderId()
     * @method null|\int[]                       getUfCurrencyPairIdList()
     * @method null|\int[]                       fillUfCurrencyPairId()
     * @method null|\string[]                    getUfSideList()
     * @method null|\string[]                    fillUfSide()
     * @method null|\float[]                     getUfPriceList()
     * @method null|\float[]                     fillUfPrice()
     * @method null|\float[]                     getUfQuantityList()
     * @method null|\float[]                     fillUfQuantity()
     * @method null|\float[]                     getUfMinAmountList()
     * @method null|\float[]                     fillUfMinAmount()
     * @method null|\float[]                     getUfMaxAmountList()
     * @method null|\float[]                     fillUfMaxAmount()
     * @method null|\string[]                    getUfCounterpartyNameList()
     * @method null|\string[]                    fillUfCounterpartyName()
     * @method null|\float[]                     getUfCounterpartyRatingList()
     * @method null|\float[]                     fillUfCounterpartyRating()
     * @method null|\int[]                       getUfCounterpartyTradesList()
     * @method null|\int[]                       fillUfCounterpartyTrades()
     * @method null|\float[]                     getUfCounterpartyCompletionRateList()
     * @method null|\float[]                     fillUfCounterpartyCompletionRate()
     * @method null|\string[]                    getUfPaymentMethodIdsList()
     * @method null|\string[]                    fillUfPaymentMethodIds()
     * @method null|\int[]                       getUfPaymentTimeLimitList()
     * @method null|\int[]                       fillUfPaymentTimeLimit()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfSyncedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfSyncedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitOrderBook $object)
     * @method        bool                                             has(\EO_RebitOrderBook $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitOrderBook                               getByPrimary($primary)
     * @method        \EO_RebitOrderBook[]                             getAll()
     * @method        bool                                             remove(\EO_RebitOrderBook $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitOrderBook_Collection                    wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitOrderBook                               current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitOrderBook_Collection                    merge(?\EO_RebitOrderBook_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitOrderBook_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitOrderBookTable
        public static $dataClass = '\RebitOrderBookTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitOrderBook_Query       query()
     * @method static EO_RebitOrderBook_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitOrderBook_Result      getById($id)
     * @method static EO_RebitOrderBook_Result      getList(array $parameters = [])
     * @method static EO_RebitOrderBook_Entity      getEntity()
     * @method static \EO_RebitOrderBook            createObject($setDefaultValues = true)
     * @method static \EO_RebitOrderBook_Collection createCollection()
     * @method static \EO_RebitOrderBook            wakeUpObject($row)
     * @method static \EO_RebitOrderBook_Collection wakeUpCollection($rows)
     */
    class RebitOrderBookTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitOrderBook_Result      exec()
     * @method \EO_RebitOrderBook            fetchObject()
     * @method \EO_RebitOrderBook_Collection fetchCollection()
     */
    class EO_RebitOrderBook_Query extends Query {}
    /**
     * @method \EO_RebitOrderBook            fetchObject()
     * @method \EO_RebitOrderBook_Collection fetchCollection()
     */
    class EO_RebitOrderBook_Result extends Result {}
    /**
     * @method \EO_RebitOrderBook            createObject($setDefaultValues = true)
     * @method \EO_RebitOrderBook_Collection createCollection()
     * @method \EO_RebitOrderBook            wakeUpObject($row)
     * @method \EO_RebitOrderBook_Collection wakeUpCollection($rows)
     */
    class EO_RebitOrderBook_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitAdvertisementTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitAdvertisement
     *
     * @see RebitAdvertisementTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitAdvertisement          setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\int                       getUfUserId()
     * @method \EO_RebitAdvertisement          setUfUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                            hasUfUserId()
     * @method bool                            isUfUserIdFilled()
     * @method bool                            isUfUserIdChanged()
     * @method null|\int                       remindActualUfUserId()
     * @method null|\int                       requireUfUserId()
     * @method \EO_RebitAdvertisement          resetUfUserId()
     * @method \EO_RebitAdvertisement          unsetUfUserId()
     * @method null|\int                       fillUfUserId()
     * @method null|\string                    getUfBybitAdId()
     * @method \EO_RebitAdvertisement          setUfBybitAdId(null|\Bitrix\Main\DB\SqlExpression|\string $ufBybitAdId)
     * @method bool                            hasUfBybitAdId()
     * @method bool                            isUfBybitAdIdFilled()
     * @method bool                            isUfBybitAdIdChanged()
     * @method null|\string                    remindActualUfBybitAdId()
     * @method null|\string                    requireUfBybitAdId()
     * @method \EO_RebitAdvertisement          resetUfBybitAdId()
     * @method \EO_RebitAdvertisement          unsetUfBybitAdId()
     * @method null|\string                    fillUfBybitAdId()
     * @method null|\int                       getUfCurrencyPairId()
     * @method \EO_RebitAdvertisement          setUfCurrencyPairId(null|\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyPairId)
     * @method bool                            hasUfCurrencyPairId()
     * @method bool                            isUfCurrencyPairIdFilled()
     * @method bool                            isUfCurrencyPairIdChanged()
     * @method null|\int                       remindActualUfCurrencyPairId()
     * @method null|\int                       requireUfCurrencyPairId()
     * @method \EO_RebitAdvertisement          resetUfCurrencyPairId()
     * @method \EO_RebitAdvertisement          unsetUfCurrencyPairId()
     * @method null|\int                       fillUfCurrencyPairId()
     * @method null|\string                    getUfSide()
     * @method \EO_RebitAdvertisement          setUfSide(null|\Bitrix\Main\DB\SqlExpression|\string $ufSide)
     * @method bool                            hasUfSide()
     * @method bool                            isUfSideFilled()
     * @method bool                            isUfSideChanged()
     * @method null|\string                    remindActualUfSide()
     * @method null|\string                    requireUfSide()
     * @method \EO_RebitAdvertisement          resetUfSide()
     * @method \EO_RebitAdvertisement          unsetUfSide()
     * @method null|\string                    fillUfSide()
     * @method null|\string                    getUfPriceType()
     * @method \EO_RebitAdvertisement          setUfPriceType(null|\Bitrix\Main\DB\SqlExpression|\string $ufPriceType)
     * @method bool                            hasUfPriceType()
     * @method bool                            isUfPriceTypeFilled()
     * @method bool                            isUfPriceTypeChanged()
     * @method null|\string                    remindActualUfPriceType()
     * @method null|\string                    requireUfPriceType()
     * @method \EO_RebitAdvertisement          resetUfPriceType()
     * @method \EO_RebitAdvertisement          unsetUfPriceType()
     * @method null|\string                    fillUfPriceType()
     * @method null|\float                     getUfPrice()
     * @method \EO_RebitAdvertisement          setUfPrice(null|\Bitrix\Main\DB\SqlExpression|\float $ufPrice)
     * @method bool                            hasUfPrice()
     * @method bool                            isUfPriceFilled()
     * @method bool                            isUfPriceChanged()
     * @method null|\float                     remindActualUfPrice()
     * @method null|\float                     requireUfPrice()
     * @method \EO_RebitAdvertisement          resetUfPrice()
     * @method \EO_RebitAdvertisement          unsetUfPrice()
     * @method null|\float                     fillUfPrice()
     * @method null|\float                     getUfQuantity()
     * @method \EO_RebitAdvertisement          setUfQuantity(null|\Bitrix\Main\DB\SqlExpression|\float $ufQuantity)
     * @method bool                            hasUfQuantity()
     * @method bool                            isUfQuantityFilled()
     * @method bool                            isUfQuantityChanged()
     * @method null|\float                     remindActualUfQuantity()
     * @method null|\float                     requireUfQuantity()
     * @method \EO_RebitAdvertisement          resetUfQuantity()
     * @method \EO_RebitAdvertisement          unsetUfQuantity()
     * @method null|\float                     fillUfQuantity()
     * @method null|\float                     getUfQuantityRemaining()
     * @method \EO_RebitAdvertisement          setUfQuantityRemaining(null|\Bitrix\Main\DB\SqlExpression|\float $ufQuantityRemaining)
     * @method bool                            hasUfQuantityRemaining()
     * @method bool                            isUfQuantityRemainingFilled()
     * @method bool                            isUfQuantityRemainingChanged()
     * @method null|\float                     remindActualUfQuantityRemaining()
     * @method null|\float                     requireUfQuantityRemaining()
     * @method \EO_RebitAdvertisement          resetUfQuantityRemaining()
     * @method \EO_RebitAdvertisement          unsetUfQuantityRemaining()
     * @method null|\float                     fillUfQuantityRemaining()
     * @method null|\float                     getUfMinAmount()
     * @method \EO_RebitAdvertisement          setUfMinAmount(null|\Bitrix\Main\DB\SqlExpression|\float $ufMinAmount)
     * @method bool                            hasUfMinAmount()
     * @method bool                            isUfMinAmountFilled()
     * @method bool                            isUfMinAmountChanged()
     * @method null|\float                     remindActualUfMinAmount()
     * @method null|\float                     requireUfMinAmount()
     * @method \EO_RebitAdvertisement          resetUfMinAmount()
     * @method \EO_RebitAdvertisement          unsetUfMinAmount()
     * @method null|\float                     fillUfMinAmount()
     * @method null|\float                     getUfMaxAmount()
     * @method \EO_RebitAdvertisement          setUfMaxAmount(null|\Bitrix\Main\DB\SqlExpression|\float $ufMaxAmount)
     * @method bool                            hasUfMaxAmount()
     * @method bool                            isUfMaxAmountFilled()
     * @method bool                            isUfMaxAmountChanged()
     * @method null|\float                     remindActualUfMaxAmount()
     * @method null|\float                     requireUfMaxAmount()
     * @method \EO_RebitAdvertisement          resetUfMaxAmount()
     * @method \EO_RebitAdvertisement          unsetUfMaxAmount()
     * @method null|\float                     fillUfMaxAmount()
     * @method null|\string                    getUfPaymentMethodIds()
     * @method \EO_RebitAdvertisement          setUfPaymentMethodIds(null|\Bitrix\Main\DB\SqlExpression|\string $ufPaymentMethodIds)
     * @method bool                            hasUfPaymentMethodIds()
     * @method bool                            isUfPaymentMethodIdsFilled()
     * @method bool                            isUfPaymentMethodIdsChanged()
     * @method null|\string                    remindActualUfPaymentMethodIds()
     * @method null|\string                    requireUfPaymentMethodIds()
     * @method \EO_RebitAdvertisement          resetUfPaymentMethodIds()
     * @method \EO_RebitAdvertisement          unsetUfPaymentMethodIds()
     * @method null|\string                    fillUfPaymentMethodIds()
     * @method null|\string                    getUfConditions()
     * @method \EO_RebitAdvertisement          setUfConditions(null|\Bitrix\Main\DB\SqlExpression|\string $ufConditions)
     * @method bool                            hasUfConditions()
     * @method bool                            isUfConditionsFilled()
     * @method bool                            isUfConditionsChanged()
     * @method null|\string                    remindActualUfConditions()
     * @method null|\string                    requireUfConditions()
     * @method \EO_RebitAdvertisement          resetUfConditions()
     * @method \EO_RebitAdvertisement          unsetUfConditions()
     * @method null|\string                    fillUfConditions()
     * @method null|\int                       getUfChatScriptId()
     * @method \EO_RebitAdvertisement          setUfChatScriptId(null|\Bitrix\Main\DB\SqlExpression|\int $ufChatScriptId)
     * @method bool                            hasUfChatScriptId()
     * @method bool                            isUfChatScriptIdFilled()
     * @method bool                            isUfChatScriptIdChanged()
     * @method null|\int                       remindActualUfChatScriptId()
     * @method null|\int                       requireUfChatScriptId()
     * @method \EO_RebitAdvertisement          resetUfChatScriptId()
     * @method \EO_RebitAdvertisement          unsetUfChatScriptId()
     * @method null|\int                       fillUfChatScriptId()
     * @method null|\string                    getUfStatus()
     * @method \EO_RebitAdvertisement          setUfStatus(null|\Bitrix\Main\DB\SqlExpression|\string $ufStatus)
     * @method bool                            hasUfStatus()
     * @method bool                            isUfStatusFilled()
     * @method bool                            isUfStatusChanged()
     * @method null|\string                    remindActualUfStatus()
     * @method null|\string                    requireUfStatus()
     * @method \EO_RebitAdvertisement          resetUfStatus()
     * @method \EO_RebitAdvertisement          unsetUfStatus()
     * @method null|\string                    fillUfStatus()
     * @method null|\Bitrix\Main\Type\DateTime getUfCreatedAt()
     * @method \EO_RebitAdvertisement          setUfCreatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                            hasUfCreatedAt()
     * @method bool                            isUfCreatedAtFilled()
     * @method bool                            isUfCreatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCreatedAt()
     * @method \EO_RebitAdvertisement          resetUfCreatedAt()
     * @method \EO_RebitAdvertisement          unsetUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfUpdatedAt()
     * @method \EO_RebitAdvertisement          setUfUpdatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                            hasUfUpdatedAt()
     * @method bool                            isUfUpdatedAtFilled()
     * @method bool                            isUfUpdatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfUpdatedAt()
     * @method \EO_RebitAdvertisement          resetUfUpdatedAt()
     * @method \EO_RebitAdvertisement          unsetUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfUpdatedAt()
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
     * @method        \EO_RebitAdvertisement                                                                          set($fieldName, $value)
     * @method        \EO_RebitAdvertisement                                                                          reset($fieldName)
     * @method        \EO_RebitAdvertisement                                                                          unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitAdvertisement                                                                          wakeUp($data)
     */
    class EO_RebitAdvertisement
    {
        // @var \RebitAdvertisementTable
        public static $dataClass = '\RebitAdvertisementTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitAdvertisement_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\int[]                       getUfUserIdList()
     * @method null|\int[]                       fillUfUserId()
     * @method null|\string[]                    getUfBybitAdIdList()
     * @method null|\string[]                    fillUfBybitAdId()
     * @method null|\int[]                       getUfCurrencyPairIdList()
     * @method null|\int[]                       fillUfCurrencyPairId()
     * @method null|\string[]                    getUfSideList()
     * @method null|\string[]                    fillUfSide()
     * @method null|\string[]                    getUfPriceTypeList()
     * @method null|\string[]                    fillUfPriceType()
     * @method null|\float[]                     getUfPriceList()
     * @method null|\float[]                     fillUfPrice()
     * @method null|\float[]                     getUfQuantityList()
     * @method null|\float[]                     fillUfQuantity()
     * @method null|\float[]                     getUfQuantityRemainingList()
     * @method null|\float[]                     fillUfQuantityRemaining()
     * @method null|\float[]                     getUfMinAmountList()
     * @method null|\float[]                     fillUfMinAmount()
     * @method null|\float[]                     getUfMaxAmountList()
     * @method null|\float[]                     fillUfMaxAmount()
     * @method null|\string[]                    getUfPaymentMethodIdsList()
     * @method null|\string[]                    fillUfPaymentMethodIds()
     * @method null|\string[]                    getUfConditionsList()
     * @method null|\string[]                    fillUfConditions()
     * @method null|\int[]                       getUfChatScriptIdList()
     * @method null|\int[]                       fillUfChatScriptId()
     * @method null|\string[]                    getUfStatusList()
     * @method null|\string[]                    fillUfStatus()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfUpdatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitAdvertisement $object)
     * @method        bool                                             has(\EO_RebitAdvertisement $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitAdvertisement                           getByPrimary($primary)
     * @method        \EO_RebitAdvertisement[]                         getAll()
     * @method        bool                                             remove(\EO_RebitAdvertisement $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitAdvertisement_Collection                wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitAdvertisement                           current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitAdvertisement_Collection                merge(?\EO_RebitAdvertisement_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitAdvertisement_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitAdvertisementTable
        public static $dataClass = '\RebitAdvertisementTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitAdvertisement_Query       query()
     * @method static EO_RebitAdvertisement_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitAdvertisement_Result      getById($id)
     * @method static EO_RebitAdvertisement_Result      getList(array $parameters = [])
     * @method static EO_RebitAdvertisement_Entity      getEntity()
     * @method static \EO_RebitAdvertisement            createObject($setDefaultValues = true)
     * @method static \EO_RebitAdvertisement_Collection createCollection()
     * @method static \EO_RebitAdvertisement            wakeUpObject($row)
     * @method static \EO_RebitAdvertisement_Collection wakeUpCollection($rows)
     */
    class RebitAdvertisementTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitAdvertisement_Result      exec()
     * @method \EO_RebitAdvertisement            fetchObject()
     * @method \EO_RebitAdvertisement_Collection fetchCollection()
     */
    class EO_RebitAdvertisement_Query extends Query {}
    /**
     * @method \EO_RebitAdvertisement            fetchObject()
     * @method \EO_RebitAdvertisement_Collection fetchCollection()
     */
    class EO_RebitAdvertisement_Result extends Result {}
    /**
     * @method \EO_RebitAdvertisement            createObject($setDefaultValues = true)
     * @method \EO_RebitAdvertisement_Collection createCollection()
     * @method \EO_RebitAdvertisement            wakeUpObject($row)
     * @method \EO_RebitAdvertisement_Collection wakeUpCollection($rows)
     */
    class EO_RebitAdvertisement_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitTradeTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitTrade
     *
     * @see RebitTradeTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitTrade                  setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\string                    getUfBybitOrderId()
     * @method \EO_RebitTrade                  setUfBybitOrderId(null|\Bitrix\Main\DB\SqlExpression|\string $ufBybitOrderId)
     * @method bool                            hasUfBybitOrderId()
     * @method bool                            isUfBybitOrderIdFilled()
     * @method bool                            isUfBybitOrderIdChanged()
     * @method null|\string                    remindActualUfBybitOrderId()
     * @method null|\string                    requireUfBybitOrderId()
     * @method \EO_RebitTrade                  resetUfBybitOrderId()
     * @method \EO_RebitTrade                  unsetUfBybitOrderId()
     * @method null|\string                    fillUfBybitOrderId()
     * @method null|\int                       getUfBuyerUserId()
     * @method \EO_RebitTrade                  setUfBuyerUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufBuyerUserId)
     * @method bool                            hasUfBuyerUserId()
     * @method bool                            isUfBuyerUserIdFilled()
     * @method bool                            isUfBuyerUserIdChanged()
     * @method null|\int                       remindActualUfBuyerUserId()
     * @method null|\int                       requireUfBuyerUserId()
     * @method \EO_RebitTrade                  resetUfBuyerUserId()
     * @method \EO_RebitTrade                  unsetUfBuyerUserId()
     * @method null|\int                       fillUfBuyerUserId()
     * @method null|\int                       getUfSellerUserId()
     * @method \EO_RebitTrade                  setUfSellerUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufSellerUserId)
     * @method bool                            hasUfSellerUserId()
     * @method bool                            isUfSellerUserIdFilled()
     * @method bool                            isUfSellerUserIdChanged()
     * @method null|\int                       remindActualUfSellerUserId()
     * @method null|\int                       requireUfSellerUserId()
     * @method \EO_RebitTrade                  resetUfSellerUserId()
     * @method \EO_RebitTrade                  unsetUfSellerUserId()
     * @method null|\int                       fillUfSellerUserId()
     * @method null|\int                       getUfAdvertisementId()
     * @method \EO_RebitTrade                  setUfAdvertisementId(null|\Bitrix\Main\DB\SqlExpression|\int $ufAdvertisementId)
     * @method bool                            hasUfAdvertisementId()
     * @method bool                            isUfAdvertisementIdFilled()
     * @method bool                            isUfAdvertisementIdChanged()
     * @method null|\int                       remindActualUfAdvertisementId()
     * @method null|\int                       requireUfAdvertisementId()
     * @method \EO_RebitTrade                  resetUfAdvertisementId()
     * @method \EO_RebitTrade                  unsetUfAdvertisementId()
     * @method null|\int                       fillUfAdvertisementId()
     * @method null|\int                       getUfOrderBookEntryId()
     * @method \EO_RebitTrade                  setUfOrderBookEntryId(null|\Bitrix\Main\DB\SqlExpression|\int $ufOrderBookEntryId)
     * @method bool                            hasUfOrderBookEntryId()
     * @method bool                            isUfOrderBookEntryIdFilled()
     * @method bool                            isUfOrderBookEntryIdChanged()
     * @method null|\int                       remindActualUfOrderBookEntryId()
     * @method null|\int                       requireUfOrderBookEntryId()
     * @method \EO_RebitTrade                  resetUfOrderBookEntryId()
     * @method \EO_RebitTrade                  unsetUfOrderBookEntryId()
     * @method null|\int                       fillUfOrderBookEntryId()
     * @method null|\int                       getUfCurrencyPairId()
     * @method \EO_RebitTrade                  setUfCurrencyPairId(null|\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyPairId)
     * @method bool                            hasUfCurrencyPairId()
     * @method bool                            isUfCurrencyPairIdFilled()
     * @method bool                            isUfCurrencyPairIdChanged()
     * @method null|\int                       remindActualUfCurrencyPairId()
     * @method null|\int                       requireUfCurrencyPairId()
     * @method \EO_RebitTrade                  resetUfCurrencyPairId()
     * @method \EO_RebitTrade                  unsetUfCurrencyPairId()
     * @method null|\int                       fillUfCurrencyPairId()
     * @method null|\string                    getUfSide()
     * @method \EO_RebitTrade                  setUfSide(null|\Bitrix\Main\DB\SqlExpression|\string $ufSide)
     * @method bool                            hasUfSide()
     * @method bool                            isUfSideFilled()
     * @method bool                            isUfSideChanged()
     * @method null|\string                    remindActualUfSide()
     * @method null|\string                    requireUfSide()
     * @method \EO_RebitTrade                  resetUfSide()
     * @method \EO_RebitTrade                  unsetUfSide()
     * @method null|\string                    fillUfSide()
     * @method null|\float                     getUfPrice()
     * @method \EO_RebitTrade                  setUfPrice(null|\Bitrix\Main\DB\SqlExpression|\float $ufPrice)
     * @method bool                            hasUfPrice()
     * @method bool                            isUfPriceFilled()
     * @method bool                            isUfPriceChanged()
     * @method null|\float                     remindActualUfPrice()
     * @method null|\float                     requireUfPrice()
     * @method \EO_RebitTrade                  resetUfPrice()
     * @method \EO_RebitTrade                  unsetUfPrice()
     * @method null|\float                     fillUfPrice()
     * @method null|\float                     getUfQuantity()
     * @method \EO_RebitTrade                  setUfQuantity(null|\Bitrix\Main\DB\SqlExpression|\float $ufQuantity)
     * @method bool                            hasUfQuantity()
     * @method bool                            isUfQuantityFilled()
     * @method bool                            isUfQuantityChanged()
     * @method null|\float                     remindActualUfQuantity()
     * @method null|\float                     requireUfQuantity()
     * @method \EO_RebitTrade                  resetUfQuantity()
     * @method \EO_RebitTrade                  unsetUfQuantity()
     * @method null|\float                     fillUfQuantity()
     * @method null|\float                     getUfFiatAmount()
     * @method \EO_RebitTrade                  setUfFiatAmount(null|\Bitrix\Main\DB\SqlExpression|\float $ufFiatAmount)
     * @method bool                            hasUfFiatAmount()
     * @method bool                            isUfFiatAmountFilled()
     * @method bool                            isUfFiatAmountChanged()
     * @method null|\float                     remindActualUfFiatAmount()
     * @method null|\float                     requireUfFiatAmount()
     * @method \EO_RebitTrade                  resetUfFiatAmount()
     * @method \EO_RebitTrade                  unsetUfFiatAmount()
     * @method null|\float                     fillUfFiatAmount()
     * @method null|\float                     getUfFee()
     * @method \EO_RebitTrade                  setUfFee(null|\Bitrix\Main\DB\SqlExpression|\float $ufFee)
     * @method bool                            hasUfFee()
     * @method bool                            isUfFeeFilled()
     * @method bool                            isUfFeeChanged()
     * @method null|\float                     remindActualUfFee()
     * @method null|\float                     requireUfFee()
     * @method \EO_RebitTrade                  resetUfFee()
     * @method \EO_RebitTrade                  unsetUfFee()
     * @method null|\float                     fillUfFee()
     * @method null|\int                       getUfPaymentMethodId()
     * @method \EO_RebitTrade                  setUfPaymentMethodId(null|\Bitrix\Main\DB\SqlExpression|\int $ufPaymentMethodId)
     * @method bool                            hasUfPaymentMethodId()
     * @method bool                            isUfPaymentMethodIdFilled()
     * @method bool                            isUfPaymentMethodIdChanged()
     * @method null|\int                       remindActualUfPaymentMethodId()
     * @method null|\int                       requireUfPaymentMethodId()
     * @method \EO_RebitTrade                  resetUfPaymentMethodId()
     * @method \EO_RebitTrade                  unsetUfPaymentMethodId()
     * @method null|\int                       fillUfPaymentMethodId()
     * @method null|\string                    getUfPaymentDetails()
     * @method \EO_RebitTrade                  setUfPaymentDetails(null|\Bitrix\Main\DB\SqlExpression|\string $ufPaymentDetails)
     * @method bool                            hasUfPaymentDetails()
     * @method bool                            isUfPaymentDetailsFilled()
     * @method bool                            isUfPaymentDetailsChanged()
     * @method null|\string                    remindActualUfPaymentDetails()
     * @method null|\string                    requireUfPaymentDetails()
     * @method \EO_RebitTrade                  resetUfPaymentDetails()
     * @method \EO_RebitTrade                  unsetUfPaymentDetails()
     * @method null|\string                    fillUfPaymentDetails()
     * @method null|\string                    getUfComment()
     * @method \EO_RebitTrade                  setUfComment(null|\Bitrix\Main\DB\SqlExpression|\string $ufComment)
     * @method bool                            hasUfComment()
     * @method bool                            isUfCommentFilled()
     * @method bool                            isUfCommentChanged()
     * @method null|\string                    remindActualUfComment()
     * @method null|\string                    requireUfComment()
     * @method \EO_RebitTrade                  resetUfComment()
     * @method \EO_RebitTrade                  unsetUfComment()
     * @method null|\string                    fillUfComment()
     * @method null|\string                    getUfStatus()
     * @method \EO_RebitTrade                  setUfStatus(null|\Bitrix\Main\DB\SqlExpression|\string $ufStatus)
     * @method bool                            hasUfStatus()
     * @method bool                            isUfStatusFilled()
     * @method bool                            isUfStatusChanged()
     * @method null|\string                    remindActualUfStatus()
     * @method null|\string                    requireUfStatus()
     * @method \EO_RebitTrade                  resetUfStatus()
     * @method \EO_RebitTrade                  unsetUfStatus()
     * @method null|\string                    fillUfStatus()
     * @method null|\Bitrix\Main\Type\DateTime getUfPaymentDeadline()
     * @method \EO_RebitTrade                  setUfPaymentDeadline(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufPaymentDeadline)
     * @method bool                            hasUfPaymentDeadline()
     * @method bool                            isUfPaymentDeadlineFilled()
     * @method bool                            isUfPaymentDeadlineChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfPaymentDeadline()
     * @method null|\Bitrix\Main\Type\DateTime requireUfPaymentDeadline()
     * @method \EO_RebitTrade                  resetUfPaymentDeadline()
     * @method \EO_RebitTrade                  unsetUfPaymentDeadline()
     * @method null|\Bitrix\Main\Type\DateTime fillUfPaymentDeadline()
     * @method null|\Bitrix\Main\Type\DateTime getUfPaidAt()
     * @method \EO_RebitTrade                  setUfPaidAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufPaidAt)
     * @method bool                            hasUfPaidAt()
     * @method bool                            isUfPaidAtFilled()
     * @method bool                            isUfPaidAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfPaidAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfPaidAt()
     * @method \EO_RebitTrade                  resetUfPaidAt()
     * @method \EO_RebitTrade                  unsetUfPaidAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfPaidAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfConfirmedAt()
     * @method \EO_RebitTrade                  setUfConfirmedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufConfirmedAt)
     * @method bool                            hasUfConfirmedAt()
     * @method bool                            isUfConfirmedAtFilled()
     * @method bool                            isUfConfirmedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfConfirmedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfConfirmedAt()
     * @method \EO_RebitTrade                  resetUfConfirmedAt()
     * @method \EO_RebitTrade                  unsetUfConfirmedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfConfirmedAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfCompletedAt()
     * @method \EO_RebitTrade                  setUfCompletedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCompletedAt)
     * @method bool                            hasUfCompletedAt()
     * @method bool                            isUfCompletedAtFilled()
     * @method bool                            isUfCompletedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCompletedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCompletedAt()
     * @method \EO_RebitTrade                  resetUfCompletedAt()
     * @method \EO_RebitTrade                  unsetUfCompletedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfCompletedAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfCancelledAt()
     * @method \EO_RebitTrade                  setUfCancelledAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCancelledAt)
     * @method bool                            hasUfCancelledAt()
     * @method bool                            isUfCancelledAtFilled()
     * @method bool                            isUfCancelledAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCancelledAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCancelledAt()
     * @method \EO_RebitTrade                  resetUfCancelledAt()
     * @method \EO_RebitTrade                  unsetUfCancelledAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfCancelledAt()
     * @method null|\string                    getUfCancelReason()
     * @method \EO_RebitTrade                  setUfCancelReason(null|\Bitrix\Main\DB\SqlExpression|\string $ufCancelReason)
     * @method bool                            hasUfCancelReason()
     * @method bool                            isUfCancelReasonFilled()
     * @method bool                            isUfCancelReasonChanged()
     * @method null|\string                    remindActualUfCancelReason()
     * @method null|\string                    requireUfCancelReason()
     * @method \EO_RebitTrade                  resetUfCancelReason()
     * @method \EO_RebitTrade                  unsetUfCancelReason()
     * @method null|\string                    fillUfCancelReason()
     * @method null|\string                    getUfCounterpartyName()
     * @method \EO_RebitTrade                  setUfCounterpartyName(null|\Bitrix\Main\DB\SqlExpression|\string $ufCounterpartyName)
     * @method bool                            hasUfCounterpartyName()
     * @method bool                            isUfCounterpartyNameFilled()
     * @method bool                            isUfCounterpartyNameChanged()
     * @method null|\string                    remindActualUfCounterpartyName()
     * @method null|\string                    requireUfCounterpartyName()
     * @method \EO_RebitTrade                  resetUfCounterpartyName()
     * @method \EO_RebitTrade                  unsetUfCounterpartyName()
     * @method null|\string                    fillUfCounterpartyName()
     * @method null|\Bitrix\Main\Type\DateTime getUfCreatedAt()
     * @method \EO_RebitTrade                  setUfCreatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                            hasUfCreatedAt()
     * @method bool                            isUfCreatedAtFilled()
     * @method bool                            isUfCreatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCreatedAt()
     * @method \EO_RebitTrade                  resetUfCreatedAt()
     * @method \EO_RebitTrade                  unsetUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfUpdatedAt()
     * @method \EO_RebitTrade                  setUfUpdatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                            hasUfUpdatedAt()
     * @method bool                            isUfUpdatedAtFilled()
     * @method bool                            isUfUpdatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfUpdatedAt()
     * @method \EO_RebitTrade                  resetUfUpdatedAt()
     * @method \EO_RebitTrade                  unsetUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfUpdatedAt()
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
     * @method        \EO_RebitTrade                                                                                  set($fieldName, $value)
     * @method        \EO_RebitTrade                                                                                  reset($fieldName)
     * @method        \EO_RebitTrade                                                                                  unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitTrade                                                                                  wakeUp($data)
     */
    class EO_RebitTrade
    {
        // @var \RebitTradeTable
        public static $dataClass = '\RebitTradeTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitTrade_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\string[]                    getUfBybitOrderIdList()
     * @method null|\string[]                    fillUfBybitOrderId()
     * @method null|\int[]                       getUfBuyerUserIdList()
     * @method null|\int[]                       fillUfBuyerUserId()
     * @method null|\int[]                       getUfSellerUserIdList()
     * @method null|\int[]                       fillUfSellerUserId()
     * @method null|\int[]                       getUfAdvertisementIdList()
     * @method null|\int[]                       fillUfAdvertisementId()
     * @method null|\int[]                       getUfOrderBookEntryIdList()
     * @method null|\int[]                       fillUfOrderBookEntryId()
     * @method null|\int[]                       getUfCurrencyPairIdList()
     * @method null|\int[]                       fillUfCurrencyPairId()
     * @method null|\string[]                    getUfSideList()
     * @method null|\string[]                    fillUfSide()
     * @method null|\float[]                     getUfPriceList()
     * @method null|\float[]                     fillUfPrice()
     * @method null|\float[]                     getUfQuantityList()
     * @method null|\float[]                     fillUfQuantity()
     * @method null|\float[]                     getUfFiatAmountList()
     * @method null|\float[]                     fillUfFiatAmount()
     * @method null|\float[]                     getUfFeeList()
     * @method null|\float[]                     fillUfFee()
     * @method null|\int[]                       getUfPaymentMethodIdList()
     * @method null|\int[]                       fillUfPaymentMethodId()
     * @method null|\string[]                    getUfPaymentDetailsList()
     * @method null|\string[]                    fillUfPaymentDetails()
     * @method null|\string[]                    getUfCommentList()
     * @method null|\string[]                    fillUfComment()
     * @method null|\string[]                    getUfStatusList()
     * @method null|\string[]                    fillUfStatus()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfPaymentDeadlineList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfPaymentDeadline()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfPaidAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfPaidAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfConfirmedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfConfirmedAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCompletedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCompletedAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCancelledAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCancelledAt()
     * @method null|\string[]                    getUfCancelReasonList()
     * @method null|\string[]                    fillUfCancelReason()
     * @method null|\string[]                    getUfCounterpartyNameList()
     * @method null|\string[]                    fillUfCounterpartyName()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfUpdatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitTrade $object)
     * @method        bool                                             has(\EO_RebitTrade $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitTrade                                   getByPrimary($primary)
     * @method        \EO_RebitTrade[]                                 getAll()
     * @method        bool                                             remove(\EO_RebitTrade $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitTrade_Collection                        wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitTrade                                   current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitTrade_Collection                        merge(?\EO_RebitTrade_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitTrade_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitTradeTable
        public static $dataClass = '\RebitTradeTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitTrade_Query       query()
     * @method static EO_RebitTrade_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitTrade_Result      getById($id)
     * @method static EO_RebitTrade_Result      getList(array $parameters = [])
     * @method static EO_RebitTrade_Entity      getEntity()
     * @method static \EO_RebitTrade            createObject($setDefaultValues = true)
     * @method static \EO_RebitTrade_Collection createCollection()
     * @method static \EO_RebitTrade            wakeUpObject($row)
     * @method static \EO_RebitTrade_Collection wakeUpCollection($rows)
     */
    class RebitTradeTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitTrade_Result      exec()
     * @method \EO_RebitTrade            fetchObject()
     * @method \EO_RebitTrade_Collection fetchCollection()
     */
    class EO_RebitTrade_Query extends Query {}
    /**
     * @method \EO_RebitTrade            fetchObject()
     * @method \EO_RebitTrade_Collection fetchCollection()
     */
    class EO_RebitTrade_Result extends Result {}
    /**
     * @method \EO_RebitTrade            createObject($setDefaultValues = true)
     * @method \EO_RebitTrade_Collection createCollection()
     * @method \EO_RebitTrade            wakeUpObject($row)
     * @method \EO_RebitTrade_Collection wakeUpCollection($rows)
     */
    class EO_RebitTrade_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitTradeMessageTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitTradeMessage
     *
     * @see RebitTradeMessageTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitTradeMessage           setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\int                       getUfTradeId()
     * @method \EO_RebitTradeMessage           setUfTradeId(null|\Bitrix\Main\DB\SqlExpression|\int $ufTradeId)
     * @method bool                            hasUfTradeId()
     * @method bool                            isUfTradeIdFilled()
     * @method bool                            isUfTradeIdChanged()
     * @method null|\int                       remindActualUfTradeId()
     * @method null|\int                       requireUfTradeId()
     * @method \EO_RebitTradeMessage           resetUfTradeId()
     * @method \EO_RebitTradeMessage           unsetUfTradeId()
     * @method null|\int                       fillUfTradeId()
     * @method null|\int                       getUfUserId()
     * @method \EO_RebitTradeMessage           setUfUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                            hasUfUserId()
     * @method bool                            isUfUserIdFilled()
     * @method bool                            isUfUserIdChanged()
     * @method null|\int                       remindActualUfUserId()
     * @method null|\int                       requireUfUserId()
     * @method \EO_RebitTradeMessage           resetUfUserId()
     * @method \EO_RebitTradeMessage           unsetUfUserId()
     * @method null|\int                       fillUfUserId()
     * @method null|\string                    getUfMessage()
     * @method \EO_RebitTradeMessage           setUfMessage(null|\Bitrix\Main\DB\SqlExpression|\string $ufMessage)
     * @method bool                            hasUfMessage()
     * @method bool                            isUfMessageFilled()
     * @method bool                            isUfMessageChanged()
     * @method null|\string                    remindActualUfMessage()
     * @method null|\string                    requireUfMessage()
     * @method \EO_RebitTradeMessage           resetUfMessage()
     * @method \EO_RebitTradeMessage           unsetUfMessage()
     * @method null|\string                    fillUfMessage()
     * @method null|\string                    getUfMessageType()
     * @method \EO_RebitTradeMessage           setUfMessageType(null|\Bitrix\Main\DB\SqlExpression|\string $ufMessageType)
     * @method bool                            hasUfMessageType()
     * @method bool                            isUfMessageTypeFilled()
     * @method bool                            isUfMessageTypeChanged()
     * @method null|\string                    remindActualUfMessageType()
     * @method null|\string                    requireUfMessageType()
     * @method \EO_RebitTradeMessage           resetUfMessageType()
     * @method \EO_RebitTradeMessage           unsetUfMessageType()
     * @method null|\string                    fillUfMessageType()
     * @method null|\int                       getUfScriptStepId()
     * @method \EO_RebitTradeMessage           setUfScriptStepId(null|\Bitrix\Main\DB\SqlExpression|\int $ufScriptStepId)
     * @method bool                            hasUfScriptStepId()
     * @method bool                            isUfScriptStepIdFilled()
     * @method bool                            isUfScriptStepIdChanged()
     * @method null|\int                       remindActualUfScriptStepId()
     * @method null|\int                       requireUfScriptStepId()
     * @method \EO_RebitTradeMessage           resetUfScriptStepId()
     * @method \EO_RebitTradeMessage           unsetUfScriptStepId()
     * @method null|\int                       fillUfScriptStepId()
     * @method null|\boolean                   getUfIsRead()
     * @method \EO_RebitTradeMessage           setUfIsRead(null|\Bitrix\Main\DB\SqlExpression|\boolean $ufIsRead)
     * @method bool                            hasUfIsRead()
     * @method bool                            isUfIsReadFilled()
     * @method bool                            isUfIsReadChanged()
     * @method null|\boolean                   remindActualUfIsRead()
     * @method null|\boolean                   requireUfIsRead()
     * @method \EO_RebitTradeMessage           resetUfIsRead()
     * @method \EO_RebitTradeMessage           unsetUfIsRead()
     * @method null|\boolean                   fillUfIsRead()
     * @method null|\Bitrix\Main\Type\DateTime getUfCreatedAt()
     * @method \EO_RebitTradeMessage           setUfCreatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                            hasUfCreatedAt()
     * @method bool                            isUfCreatedAtFilled()
     * @method bool                            isUfCreatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCreatedAt()
     * @method \EO_RebitTradeMessage           resetUfCreatedAt()
     * @method \EO_RebitTradeMessage           unsetUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfCreatedAt()
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
     * @method        \EO_RebitTradeMessage                                                                           set($fieldName, $value)
     * @method        \EO_RebitTradeMessage                                                                           reset($fieldName)
     * @method        \EO_RebitTradeMessage                                                                           unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitTradeMessage                                                                           wakeUp($data)
     */
    class EO_RebitTradeMessage
    {
        // @var \RebitTradeMessageTable
        public static $dataClass = '\RebitTradeMessageTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitTradeMessage_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\int[]                       getUfTradeIdList()
     * @method null|\int[]                       fillUfTradeId()
     * @method null|\int[]                       getUfUserIdList()
     * @method null|\int[]                       fillUfUserId()
     * @method null|\string[]                    getUfMessageList()
     * @method null|\string[]                    fillUfMessage()
     * @method null|\string[]                    getUfMessageTypeList()
     * @method null|\string[]                    fillUfMessageType()
     * @method null|\int[]                       getUfScriptStepIdList()
     * @method null|\int[]                       fillUfScriptStepId()
     * @method null|\boolean[]                   getUfIsReadList()
     * @method null|\boolean[]                   fillUfIsRead()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitTradeMessage $object)
     * @method        bool                                             has(\EO_RebitTradeMessage $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitTradeMessage                            getByPrimary($primary)
     * @method        \EO_RebitTradeMessage[]                          getAll()
     * @method        bool                                             remove(\EO_RebitTradeMessage $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitTradeMessage_Collection                 wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitTradeMessage                            current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitTradeMessage_Collection                 merge(?\EO_RebitTradeMessage_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitTradeMessage_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitTradeMessageTable
        public static $dataClass = '\RebitTradeMessageTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitTradeMessage_Query       query()
     * @method static EO_RebitTradeMessage_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitTradeMessage_Result      getById($id)
     * @method static EO_RebitTradeMessage_Result      getList(array $parameters = [])
     * @method static EO_RebitTradeMessage_Entity      getEntity()
     * @method static \EO_RebitTradeMessage            createObject($setDefaultValues = true)
     * @method static \EO_RebitTradeMessage_Collection createCollection()
     * @method static \EO_RebitTradeMessage            wakeUpObject($row)
     * @method static \EO_RebitTradeMessage_Collection wakeUpCollection($rows)
     */
    class RebitTradeMessageTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitTradeMessage_Result      exec()
     * @method \EO_RebitTradeMessage            fetchObject()
     * @method \EO_RebitTradeMessage_Collection fetchCollection()
     */
    class EO_RebitTradeMessage_Query extends Query {}
    /**
     * @method \EO_RebitTradeMessage            fetchObject()
     * @method \EO_RebitTradeMessage_Collection fetchCollection()
     */
    class EO_RebitTradeMessage_Result extends Result {}
    /**
     * @method \EO_RebitTradeMessage            createObject($setDefaultValues = true)
     * @method \EO_RebitTradeMessage_Collection createCollection()
     * @method \EO_RebitTradeMessage            wakeUpObject($row)
     * @method \EO_RebitTradeMessage_Collection wakeUpCollection($rows)
     */
    class EO_RebitTradeMessage_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitTradeChatScriptTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitTradeChatScript
     *
     * @see RebitTradeChatScriptTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitTradeChatScript        setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\int                       getUfUserId()
     * @method \EO_RebitTradeChatScript        setUfUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                            hasUfUserId()
     * @method bool                            isUfUserIdFilled()
     * @method bool                            isUfUserIdChanged()
     * @method null|\int                       remindActualUfUserId()
     * @method null|\int                       requireUfUserId()
     * @method \EO_RebitTradeChatScript        resetUfUserId()
     * @method \EO_RebitTradeChatScript        unsetUfUserId()
     * @method null|\int                       fillUfUserId()
     * @method null|\string                    getUfName()
     * @method \EO_RebitTradeChatScript        setUfName(null|\Bitrix\Main\DB\SqlExpression|\string $ufName)
     * @method bool                            hasUfName()
     * @method bool                            isUfNameFilled()
     * @method bool                            isUfNameChanged()
     * @method null|\string                    remindActualUfName()
     * @method null|\string                    requireUfName()
     * @method \EO_RebitTradeChatScript        resetUfName()
     * @method \EO_RebitTradeChatScript        unsetUfName()
     * @method null|\string                    fillUfName()
     * @method null|\boolean                   getUfIsActive()
     * @method \EO_RebitTradeChatScript        setUfIsActive(null|\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                            hasUfIsActive()
     * @method bool                            isUfIsActiveFilled()
     * @method bool                            isUfIsActiveChanged()
     * @method null|\boolean                   remindActualUfIsActive()
     * @method null|\boolean                   requireUfIsActive()
     * @method \EO_RebitTradeChatScript        resetUfIsActive()
     * @method \EO_RebitTradeChatScript        unsetUfIsActive()
     * @method null|\boolean                   fillUfIsActive()
     * @method null|\Bitrix\Main\Type\DateTime getUfCreatedAt()
     * @method \EO_RebitTradeChatScript        setUfCreatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                            hasUfCreatedAt()
     * @method bool                            isUfCreatedAtFilled()
     * @method bool                            isUfCreatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCreatedAt()
     * @method \EO_RebitTradeChatScript        resetUfCreatedAt()
     * @method \EO_RebitTradeChatScript        unsetUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfUpdatedAt()
     * @method \EO_RebitTradeChatScript        setUfUpdatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                            hasUfUpdatedAt()
     * @method bool                            isUfUpdatedAtFilled()
     * @method bool                            isUfUpdatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfUpdatedAt()
     * @method \EO_RebitTradeChatScript        resetUfUpdatedAt()
     * @method \EO_RebitTradeChatScript        unsetUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfUpdatedAt()
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
     * @method        \EO_RebitTradeChatScript                                                                        set($fieldName, $value)
     * @method        \EO_RebitTradeChatScript                                                                        reset($fieldName)
     * @method        \EO_RebitTradeChatScript                                                                        unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitTradeChatScript                                                                        wakeUp($data)
     */
    class EO_RebitTradeChatScript
    {
        // @var \RebitTradeChatScriptTable
        public static $dataClass = '\RebitTradeChatScriptTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitTradeChatScript_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\int[]                       getUfUserIdList()
     * @method null|\int[]                       fillUfUserId()
     * @method null|\string[]                    getUfNameList()
     * @method null|\string[]                    fillUfName()
     * @method null|\boolean[]                   getUfIsActiveList()
     * @method null|\boolean[]                   fillUfIsActive()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfUpdatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitTradeChatScript $object)
     * @method        bool                                             has(\EO_RebitTradeChatScript $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitTradeChatScript                         getByPrimary($primary)
     * @method        \EO_RebitTradeChatScript[]                       getAll()
     * @method        bool                                             remove(\EO_RebitTradeChatScript $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitTradeChatScript_Collection              wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitTradeChatScript                         current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitTradeChatScript_Collection              merge(?\EO_RebitTradeChatScript_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitTradeChatScript_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitTradeChatScriptTable
        public static $dataClass = '\RebitTradeChatScriptTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitTradeChatScript_Query       query()
     * @method static EO_RebitTradeChatScript_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitTradeChatScript_Result      getById($id)
     * @method static EO_RebitTradeChatScript_Result      getList(array $parameters = [])
     * @method static EO_RebitTradeChatScript_Entity      getEntity()
     * @method static \EO_RebitTradeChatScript            createObject($setDefaultValues = true)
     * @method static \EO_RebitTradeChatScript_Collection createCollection()
     * @method static \EO_RebitTradeChatScript            wakeUpObject($row)
     * @method static \EO_RebitTradeChatScript_Collection wakeUpCollection($rows)
     */
    class RebitTradeChatScriptTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitTradeChatScript_Result      exec()
     * @method \EO_RebitTradeChatScript            fetchObject()
     * @method \EO_RebitTradeChatScript_Collection fetchCollection()
     */
    class EO_RebitTradeChatScript_Query extends Query {}
    /**
     * @method \EO_RebitTradeChatScript            fetchObject()
     * @method \EO_RebitTradeChatScript_Collection fetchCollection()
     */
    class EO_RebitTradeChatScript_Result extends Result {}
    /**
     * @method \EO_RebitTradeChatScript            createObject($setDefaultValues = true)
     * @method \EO_RebitTradeChatScript_Collection createCollection()
     * @method \EO_RebitTradeChatScript            wakeUpObject($row)
     * @method \EO_RebitTradeChatScript_Collection wakeUpCollection($rows)
     */
    class EO_RebitTradeChatScript_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitTradeChatScriptStepTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitTradeChatScriptStep
     *
     * @see RebitTradeChatScriptStepTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                         getId()
     * @method \EO_RebitTradeChatScriptStep setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                         hasId()
     * @method bool                         isIdFilled()
     * @method bool                         isIdChanged()
     * @method null|\int                    getUfScriptId()
     * @method \EO_RebitTradeChatScriptStep setUfScriptId(null|\Bitrix\Main\DB\SqlExpression|\int $ufScriptId)
     * @method bool                         hasUfScriptId()
     * @method bool                         isUfScriptIdFilled()
     * @method bool                         isUfScriptIdChanged()
     * @method null|\int                    remindActualUfScriptId()
     * @method null|\int                    requireUfScriptId()
     * @method \EO_RebitTradeChatScriptStep resetUfScriptId()
     * @method \EO_RebitTradeChatScriptStep unsetUfScriptId()
     * @method null|\int                    fillUfScriptId()
     * @method null|\int                    getUfSort()
     * @method \EO_RebitTradeChatScriptStep setUfSort(null|\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                         hasUfSort()
     * @method bool                         isUfSortFilled()
     * @method bool                         isUfSortChanged()
     * @method null|\int                    remindActualUfSort()
     * @method null|\int                    requireUfSort()
     * @method \EO_RebitTradeChatScriptStep resetUfSort()
     * @method \EO_RebitTradeChatScriptStep unsetUfSort()
     * @method null|\int                    fillUfSort()
     * @method null|\string                 getUfMessage()
     * @method \EO_RebitTradeChatScriptStep setUfMessage(null|\Bitrix\Main\DB\SqlExpression|\string $ufMessage)
     * @method bool                         hasUfMessage()
     * @method bool                         isUfMessageFilled()
     * @method bool                         isUfMessageChanged()
     * @method null|\string                 remindActualUfMessage()
     * @method null|\string                 requireUfMessage()
     * @method \EO_RebitTradeChatScriptStep resetUfMessage()
     * @method \EO_RebitTradeChatScriptStep unsetUfMessage()
     * @method null|\string                 fillUfMessage()
     * @method null|\int                    getUfDelaySeconds()
     * @method \EO_RebitTradeChatScriptStep setUfDelaySeconds(null|\Bitrix\Main\DB\SqlExpression|\int $ufDelaySeconds)
     * @method bool                         hasUfDelaySeconds()
     * @method bool                         isUfDelaySecondsFilled()
     * @method bool                         isUfDelaySecondsChanged()
     * @method null|\int                    remindActualUfDelaySeconds()
     * @method null|\int                    requireUfDelaySeconds()
     * @method \EO_RebitTradeChatScriptStep resetUfDelaySeconds()
     * @method \EO_RebitTradeChatScriptStep unsetUfDelaySeconds()
     * @method null|\int                    fillUfDelaySeconds()
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
     * @method        \EO_RebitTradeChatScriptStep                                                                    set($fieldName, $value)
     * @method        \EO_RebitTradeChatScriptStep                                                                    reset($fieldName)
     * @method        \EO_RebitTradeChatScriptStep                                                                    unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitTradeChatScriptStep                                                                    wakeUp($data)
     */
    class EO_RebitTradeChatScriptStep
    {
        // @var \RebitTradeChatScriptStepTable
        public static $dataClass = '\RebitTradeChatScriptStepTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitTradeChatScriptStep_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]         getIdList()
     * @method null|\int[]    getUfScriptIdList()
     * @method null|\int[]    fillUfScriptId()
     * @method null|\int[]    getUfSortList()
     * @method null|\int[]    fillUfSort()
     * @method null|\string[] getUfMessageList()
     * @method null|\string[] fillUfMessage()
     * @method null|\int[]    getUfDelaySecondsList()
     * @method null|\int[]    fillUfDelaySeconds()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitTradeChatScriptStep $object)
     * @method        bool                                             has(\EO_RebitTradeChatScriptStep $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitTradeChatScriptStep                     getByPrimary($primary)
     * @method        \EO_RebitTradeChatScriptStep[]                   getAll()
     * @method        bool                                             remove(\EO_RebitTradeChatScriptStep $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitTradeChatScriptStep_Collection          wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitTradeChatScriptStep                     current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitTradeChatScriptStep_Collection          merge(?\EO_RebitTradeChatScriptStep_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitTradeChatScriptStep_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitTradeChatScriptStepTable
        public static $dataClass = '\RebitTradeChatScriptStepTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitTradeChatScriptStep_Query       query()
     * @method static EO_RebitTradeChatScriptStep_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitTradeChatScriptStep_Result      getById($id)
     * @method static EO_RebitTradeChatScriptStep_Result      getList(array $parameters = [])
     * @method static EO_RebitTradeChatScriptStep_Entity      getEntity()
     * @method static \EO_RebitTradeChatScriptStep            createObject($setDefaultValues = true)
     * @method static \EO_RebitTradeChatScriptStep_Collection createCollection()
     * @method static \EO_RebitTradeChatScriptStep            wakeUpObject($row)
     * @method static \EO_RebitTradeChatScriptStep_Collection wakeUpCollection($rows)
     */
    class RebitTradeChatScriptStepTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitTradeChatScriptStep_Result      exec()
     * @method \EO_RebitTradeChatScriptStep            fetchObject()
     * @method \EO_RebitTradeChatScriptStep_Collection fetchCollection()
     */
    class EO_RebitTradeChatScriptStep_Query extends Query {}
    /**
     * @method \EO_RebitTradeChatScriptStep            fetchObject()
     * @method \EO_RebitTradeChatScriptStep_Collection fetchCollection()
     */
    class EO_RebitTradeChatScriptStep_Result extends Result {}
    /**
     * @method \EO_RebitTradeChatScriptStep            createObject($setDefaultValues = true)
     * @method \EO_RebitTradeChatScriptStep_Collection createCollection()
     * @method \EO_RebitTradeChatScriptStep            wakeUpObject($row)
     * @method \EO_RebitTradeChatScriptStep_Collection wakeUpCollection($rows)
     */
    class EO_RebitTradeChatScriptStep_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptStepTable

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * ChatScriptStep
     *
     * @see ChatScriptStepTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                    getId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                    hasId()
     * @method bool                                                    isIdFilled()
     * @method bool                                                    isIdChanged()
     * @method \int                                                    getUfScriptId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep setUfScriptId(\Bitrix\Main\DB\SqlExpression|\int $ufScriptId)
     * @method bool                                                    hasUfScriptId()
     * @method bool                                                    isUfScriptIdFilled()
     * @method bool                                                    isUfScriptIdChanged()
     * @method \int                                                    remindActualUfScriptId()
     * @method \int                                                    requireUfScriptId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep resetUfScriptId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep unsetUfScriptId()
     * @method \int                                                    fillUfScriptId()
     * @method \int                                                    getUfSort()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep setUfSort(\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                                                    hasUfSort()
     * @method bool                                                    isUfSortFilled()
     * @method bool                                                    isUfSortChanged()
     * @method \int                                                    remindActualUfSort()
     * @method \int                                                    requireUfSort()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep resetUfSort()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep unsetUfSort()
     * @method \int                                                    fillUfSort()
     * @method \string                                                 getUfMessage()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep setUfMessage(\Bitrix\Main\DB\SqlExpression|\string $ufMessage)
     * @method bool                                                    hasUfMessage()
     * @method bool                                                    isUfMessageFilled()
     * @method bool                                                    isUfMessageChanged()
     * @method \string                                                 remindActualUfMessage()
     * @method \string                                                 requireUfMessage()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep resetUfMessage()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep unsetUfMessage()
     * @method \string                                                 fillUfMessage()
     * @method \int                                                    getUfDelaySeconds()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep setUfDelaySeconds(\Bitrix\Main\DB\SqlExpression|\int $ufDelaySeconds)
     * @method bool                                                    hasUfDelaySeconds()
     * @method bool                                                    isUfDelaySecondsFilled()
     * @method bool                                                    isUfDelaySecondsChanged()
     * @method \int                                                    remindActualUfDelaySeconds()
     * @method \int                                                    requireUfDelaySeconds()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep resetUfDelaySeconds()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep unsetUfDelaySeconds()
     * @method \int                                                    fillUfDelaySeconds()
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
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep                                         set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep                                         reset($fieldName)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep                                         unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep                                         wakeUp($data)
     */
    class EO_ChatScriptStep
    {
        // @var \Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptStepTable
        public static $dataClass = '\Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptStepTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * ChatScriptStepCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]    getIdList()
     * @method \int[]    getUfScriptIdList()
     * @method \int[]    fillUfScriptId()
     * @method \int[]    getUfSortList()
     * @method \int[]    fillUfSort()
     * @method \string[] getUfMessageList()
     * @method \string[] fillUfMessage()
     * @method \int[]    getUfDelaySecondsList()
     * @method \int[]    fillUfDelaySeconds()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                              add(\Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep $object)
     * @method        bool                                                              has(\Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep $object)
     * @method        bool                                                              hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep[]         getAll()
     * @method        bool                                                              remove(\Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep $object)
     * @method        void                                                              removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection                  fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                      save($ignoreEvents = false)
     * @method        void                                                              offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                              offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                              offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                              offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                              rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           current()                                                                                                                                                      Iterator
     * @method        mixed                                                             key()                                                                                                                                                          Iterator
     * @method        void                                                              next()                                                                                                                                                         Iterator
     * @method        bool                                                              valid()                                                                                                                                                        Iterator
     * @method        int                                                               count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection merge(?\Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection $collection)
     * @method        bool                                                              isEmpty()
     * @method        array                                                             collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_ChatScriptStep_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptStepTable
        public static $dataClass = '\Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptStepTable';
    }
}

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_ChatScriptStep_Query                                           query()
     * @method static EO_ChatScriptStep_Result                                          getByPrimary($primary, array $parameters = [])
     * @method static EO_ChatScriptStep_Result                                          getById($id)
     * @method static EO_ChatScriptStep_Result                                          getList(array $parameters = [])
     * @method static EO_ChatScriptStep_Entity                                          getEntity()
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection createCollection()
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection wakeUpCollection($rows)
     */
    class ChatScriptStepTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_ChatScriptStep_Result                                          exec()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           fetchObject()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection fetchCollection()
     */
    class EO_ChatScriptStep_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           fetchObject()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection fetchCollection()
     */
    class EO_ChatScriptStep_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection createCollection()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection wakeUpCollection($rows)
     */
    class EO_ChatScriptStep_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptTable

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * ChatScript
     *
     * @see ChatScriptTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                getId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                hasId()
     * @method bool                                                isIdFilled()
     * @method bool                                                isIdChanged()
     * @method \int                                                getUfUserId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript setUfUserId(\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                                                hasUfUserId()
     * @method bool                                                isUfUserIdFilled()
     * @method bool                                                isUfUserIdChanged()
     * @method \int                                                remindActualUfUserId()
     * @method \int                                                requireUfUserId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript resetUfUserId()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript unsetUfUserId()
     * @method \int                                                fillUfUserId()
     * @method \string                                             getUfName()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript setUfName(\Bitrix\Main\DB\SqlExpression|\string $ufName)
     * @method bool                                                hasUfName()
     * @method bool                                                isUfNameFilled()
     * @method bool                                                isUfNameChanged()
     * @method \string                                             remindActualUfName()
     * @method \string                                             requireUfName()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript resetUfName()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript unsetUfName()
     * @method \string                                             fillUfName()
     * @method \boolean                                            getUfIsActive()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript setUfIsActive(\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                                                hasUfIsActive()
     * @method bool                                                isUfIsActiveFilled()
     * @method bool                                                isUfIsActiveChanged()
     * @method \boolean                                            remindActualUfIsActive()
     * @method \boolean                                            requireUfIsActive()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript resetUfIsActive()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript unsetUfIsActive()
     * @method \boolean                                            fillUfIsActive()
     * @method \Bitrix\Main\Type\DateTime                          getUfCreatedAt()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript setUfCreatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                                                hasUfCreatedAt()
     * @method bool                                                isUfCreatedAtFilled()
     * @method bool                                                isUfCreatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                          remindActualUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                          requireUfCreatedAt()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript resetUfCreatedAt()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript unsetUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                          fillUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                          getUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript setUfUpdatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                                                hasUfUpdatedAt()
     * @method bool                                                isUfUpdatedAtFilled()
     * @method bool                                                isUfUpdatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                          remindActualUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                          requireUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript resetUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript unsetUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                          fillUfUpdatedAt()
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
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript                                             set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript                                             reset($fieldName)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript                                             unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript                                             wakeUp($data)
     */
    class EO_ChatScript
    {
        // @var \Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptTable
        public static $dataClass = '\Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * ChatScriptCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \int[]                       getUfUserIdList()
     * @method \int[]                       fillUfUserId()
     * @method \string[]                    getUfNameList()
     * @method \string[]                    fillUfName()
     * @method \boolean[]                   getUfIsActiveList()
     * @method \boolean[]                   fillUfIsActive()
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
     * @method        void                                                          add(\Rebit\Exchange\Domain\ChatScript\Entity\ChatScript $object)
     * @method        bool                                                          has(\Rebit\Exchange\Domain\ChatScript\Entity\ChatScript $object)
     * @method        bool                                                          hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript[]         getAll()
     * @method        bool                                                          remove(\Rebit\Exchange\Domain\ChatScript\Entity\ChatScript $object)
     * @method        void                                                          removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection              fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                  save($ignoreEvents = false)
     * @method        void                                                          offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                          offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                          offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                          offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                          rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           current()                                                                                                                                                      Iterator
     * @method        mixed                                                         key()                                                                                                                                                          Iterator
     * @method        void                                                          next()                                                                                                                                                         Iterator
     * @method        bool                                                          valid()                                                                                                                                                        Iterator
     * @method        int                                                           count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection merge(?\Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection $collection)
     * @method        bool                                                          isEmpty()
     * @method        array                                                         collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_ChatScript_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptTable
        public static $dataClass = '\Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptTable';
    }
}

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_ChatScript_Query                                           query()
     * @method static EO_ChatScript_Result                                          getByPrimary($primary, array $parameters = [])
     * @method static EO_ChatScript_Result                                          getById($id)
     * @method static EO_ChatScript_Result                                          getList(array $parameters = [])
     * @method static EO_ChatScript_Entity                                          getEntity()
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection createCollection()
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection wakeUpCollection($rows)
     */
    class ChatScriptTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_ChatScript_Result                                          exec()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           fetchObject()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection fetchCollection()
     */
    class EO_ChatScript_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           fetchObject()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection fetchCollection()
     */
    class EO_ChatScript_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection createCollection()
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScript           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection wakeUpCollection($rows)
     */
    class EO_ChatScript_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * Currency
     *
     * @see CurrencyTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                            getId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                            hasId()
     * @method bool                                            isIdFilled()
     * @method bool                                            isIdChanged()
     * @method \string                                         getUfCode()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfCode(\Bitrix\Main\DB\SqlExpression|\string $ufCode)
     * @method bool                                            hasUfCode()
     * @method bool                                            isUfCodeFilled()
     * @method bool                                            isUfCodeChanged()
     * @method \string                                         remindActualUfCode()
     * @method \string                                         requireUfCode()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfCode()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfCode()
     * @method \string                                         fillUfCode()
     * @method \string                                         getUfName()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfName(\Bitrix\Main\DB\SqlExpression|\string $ufName)
     * @method bool                                            hasUfName()
     * @method bool                                            isUfNameFilled()
     * @method bool                                            isUfNameChanged()
     * @method \string                                         remindActualUfName()
     * @method \string                                         requireUfName()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfName()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfName()
     * @method \string                                         fillUfName()
     * @method \string                                         getUfType()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfType(\Bitrix\Main\DB\SqlExpression|\string $ufType)
     * @method bool                                            hasUfType()
     * @method bool                                            isUfTypeFilled()
     * @method bool                                            isUfTypeChanged()
     * @method \string                                         remindActualUfType()
     * @method \string                                         requireUfType()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfType()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfType()
     * @method \string                                         fillUfType()
     * @method \int                                            getUfDecimals()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfDecimals(\Bitrix\Main\DB\SqlExpression|\int $ufDecimals)
     * @method bool                                            hasUfDecimals()
     * @method bool                                            isUfDecimalsFilled()
     * @method bool                                            isUfDecimalsChanged()
     * @method \int                                            remindActualUfDecimals()
     * @method \int                                            requireUfDecimals()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfDecimals()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfDecimals()
     * @method \int                                            fillUfDecimals()
     * @method \int                                            getUfIcon()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfIcon(\Bitrix\Main\DB\SqlExpression|\int $ufIcon)
     * @method bool                                            hasUfIcon()
     * @method bool                                            isUfIconFilled()
     * @method bool                                            isUfIconChanged()
     * @method \int                                            remindActualUfIcon()
     * @method \int                                            requireUfIcon()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfIcon()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfIcon()
     * @method \int                                            fillUfIcon()
     * @method \boolean                                        getUfIsActive()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfIsActive(\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                                            hasUfIsActive()
     * @method bool                                            isUfIsActiveFilled()
     * @method bool                                            isUfIsActiveChanged()
     * @method \boolean                                        remindActualUfIsActive()
     * @method \boolean                                        requireUfIsActive()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfIsActive()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfIsActive()
     * @method \boolean                                        fillUfIsActive()
     * @method \int                                            getUfSort()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency setUfSort(\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                                            hasUfSort()
     * @method bool                                            isUfSortFilled()
     * @method bool                                            isUfSortChanged()
     * @method \int                                            remindActualUfSort()
     * @method \int                                            requireUfSort()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency resetUfSort()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency unsetUfSort()
     * @method \int                                            fillUfSort()
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
     * @method        \Rebit\Exchange\Domain\Currency\Entity\Currency                                                 set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\Currency                                                 reset($fieldName)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\Currency                                                 unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\Currency\Entity\Currency                                                 wakeUp($data)
     */
    class EO_Currency
    {
        // @var \Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable
        public static $dataClass = '\Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * CurrencyCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]     getIdList()
     * @method \string[]  getUfCodeList()
     * @method \string[]  fillUfCode()
     * @method \string[]  getUfNameList()
     * @method \string[]  fillUfName()
     * @method \string[]  getUfTypeList()
     * @method \string[]  fillUfType()
     * @method \int[]     getUfDecimalsList()
     * @method \int[]     fillUfDecimals()
     * @method \int[]     getUfIconList()
     * @method \int[]     fillUfIcon()
     * @method \boolean[] getUfIsActiveList()
     * @method \boolean[] fillUfIsActive()
     * @method \int[]     getUfSortList()
     * @method \int[]     fillUfSort()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                      add(\Rebit\Exchange\Domain\Currency\Entity\Currency $object)
     * @method        bool                                                      has(\Rebit\Exchange\Domain\Currency\Entity\Currency $object)
     * @method        bool                                                      hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\Currency           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\Currency[]         getAll()
     * @method        bool                                                      remove(\Rebit\Exchange\Domain\Currency\Entity\Currency $object)
     * @method        void                                                      removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection          fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                              save($ignoreEvents = false)
     * @method        void                                                      offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                      offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                      offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                      offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                      rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\Currency\Entity\Currency           current()                                                                                                                                                      Iterator
     * @method        mixed                                                     key()                                                                                                                                                          Iterator
     * @method        void                                                      next()                                                                                                                                                         Iterator
     * @method        bool                                                      valid()                                                                                                                                                        Iterator
     * @method        int                                                       count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection merge(?\Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection $collection)
     * @method        bool                                                      isEmpty()
     * @method        array                                                     collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_Currency_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable
        public static $dataClass = '\Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable';
    }
}

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_Currency_Query                                         query()
     * @method static EO_Currency_Result                                        getByPrimary($primary, array $parameters = [])
     * @method static EO_Currency_Result                                        getById($id)
     * @method static EO_Currency_Result                                        getList(array $parameters = [])
     * @method static EO_Currency_Entity                                        getEntity()
     * @method static \Rebit\Exchange\Domain\Currency\Entity\Currency           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection createCollection()
     * @method static \Rebit\Exchange\Domain\Currency\Entity\Currency           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection wakeUpCollection($rows)
     */
    class CurrencyTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_Currency_Result                                        exec()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency           fetchObject()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection fetchCollection()
     */
    class EO_Currency_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency           fetchObject()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection fetchCollection()
     */
    class EO_Currency_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection createCollection()
     * @method \Rebit\Exchange\Domain\Currency\Entity\Currency           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection wakeUpCollection($rows)
     */
    class EO_Currency_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * CurrencyPair
     *
     * @see CurrencyPairTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                getId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                hasId()
     * @method bool                                                isIdFilled()
     * @method bool                                                isIdChanged()
     * @method \int                                                getUfTokenCurrencyId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setUfTokenCurrencyId(\Bitrix\Main\DB\SqlExpression|\int $ufTokenCurrencyId)
     * @method bool                                                hasUfTokenCurrencyId()
     * @method bool                                                isUfTokenCurrencyIdFilled()
     * @method bool                                                isUfTokenCurrencyIdChanged()
     * @method \int                                                remindActualUfTokenCurrencyId()
     * @method \int                                                requireUfTokenCurrencyId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair resetUfTokenCurrencyId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair unsetUfTokenCurrencyId()
     * @method \int                                                fillUfTokenCurrencyId()
     * @method \int                                                getUfFiatCurrencyId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setUfFiatCurrencyId(\Bitrix\Main\DB\SqlExpression|\int $ufFiatCurrencyId)
     * @method bool                                                hasUfFiatCurrencyId()
     * @method bool                                                isUfFiatCurrencyIdFilled()
     * @method bool                                                isUfFiatCurrencyIdChanged()
     * @method \int                                                remindActualUfFiatCurrencyId()
     * @method \int                                                requireUfFiatCurrencyId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair resetUfFiatCurrencyId()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair unsetUfFiatCurrencyId()
     * @method \int                                                fillUfFiatCurrencyId()
     * @method \string                                             getUfCode()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setUfCode(\Bitrix\Main\DB\SqlExpression|\string $ufCode)
     * @method bool                                                hasUfCode()
     * @method bool                                                isUfCodeFilled()
     * @method bool                                                isUfCodeChanged()
     * @method \string                                             remindActualUfCode()
     * @method \string                                             requireUfCode()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair resetUfCode()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair unsetUfCode()
     * @method \string                                             fillUfCode()
     * @method \boolean                                            getUfIsActive()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setUfIsActive(\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                                                hasUfIsActive()
     * @method bool                                                isUfIsActiveFilled()
     * @method bool                                                isUfIsActiveChanged()
     * @method \boolean                                            remindActualUfIsActive()
     * @method \boolean                                            requireUfIsActive()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair resetUfIsActive()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair unsetUfIsActive()
     * @method \boolean                                            fillUfIsActive()
     * @method \boolean                                            getUfIsDefault()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setUfIsDefault(\Bitrix\Main\DB\SqlExpression|\boolean $ufIsDefault)
     * @method bool                                                hasUfIsDefault()
     * @method bool                                                isUfIsDefaultFilled()
     * @method bool                                                isUfIsDefaultChanged()
     * @method \boolean                                            remindActualUfIsDefault()
     * @method \boolean                                            requireUfIsDefault()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair resetUfIsDefault()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair unsetUfIsDefault()
     * @method \boolean                                            fillUfIsDefault()
     * @method \int                                                getUfSort()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair setUfSort(\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                                                hasUfSort()
     * @method bool                                                isUfSortFilled()
     * @method bool                                                isUfSortChanged()
     * @method \int                                                remindActualUfSort()
     * @method \int                                                requireUfSort()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair resetUfSort()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair unsetUfSort()
     * @method \int                                                fillUfSort()
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
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair                                             set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair                                             reset($fieldName)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair                                             unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair                                             wakeUp($data)
     */
    class EO_CurrencyPair
    {
        // @var \Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable
        public static $dataClass = '\Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * CurrencyPairCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]     getIdList()
     * @method \int[]     getUfTokenCurrencyIdList()
     * @method \int[]     fillUfTokenCurrencyId()
     * @method \int[]     getUfFiatCurrencyIdList()
     * @method \int[]     fillUfFiatCurrencyId()
     * @method \string[]  getUfCodeList()
     * @method \string[]  fillUfCode()
     * @method \boolean[] getUfIsActiveList()
     * @method \boolean[] fillUfIsActive()
     * @method \boolean[] getUfIsDefaultList()
     * @method \boolean[] fillUfIsDefault()
     * @method \int[]     getUfSortList()
     * @method \int[]     fillUfSort()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                          add(\Rebit\Exchange\Domain\Currency\Entity\CurrencyPair $object)
     * @method        bool                                                          has(\Rebit\Exchange\Domain\Currency\Entity\CurrencyPair $object)
     * @method        bool                                                          hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair[]         getAll()
     * @method        bool                                                          remove(\Rebit\Exchange\Domain\Currency\Entity\CurrencyPair $object)
     * @method        void                                                          removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection              fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                  save($ignoreEvents = false)
     * @method        void                                                          offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                          offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                          offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                          offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                          rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           current()                                                                                                                                                      Iterator
     * @method        mixed                                                         key()                                                                                                                                                          Iterator
     * @method        void                                                          next()                                                                                                                                                         Iterator
     * @method        bool                                                          valid()                                                                                                                                                        Iterator
     * @method        int                                                           count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection merge(?\Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection $collection)
     * @method        bool                                                          isEmpty()
     * @method        array                                                         collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_CurrencyPair_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable
        public static $dataClass = '\Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable';
    }
}

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_CurrencyPair_Query                                         query()
     * @method static EO_CurrencyPair_Result                                        getByPrimary($primary, array $parameters = [])
     * @method static EO_CurrencyPair_Result                                        getById($id)
     * @method static EO_CurrencyPair_Result                                        getList(array $parameters = [])
     * @method static EO_CurrencyPair_Entity                                        getEntity()
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection createCollection()
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection wakeUpCollection($rows)
     */
    class CurrencyPairTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_CurrencyPair_Result                                        exec()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           fetchObject()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection fetchCollection()
     */
    class EO_CurrencyPair_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           fetchObject()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection fetchCollection()
     */
    class EO_CurrencyPair_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection createCollection()
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPair           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection wakeUpCollection($rows)
     */
    class EO_CurrencyPair_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\OrderBook\Entity\Table\OrderBookEntryTable

namespace Rebit\Exchange\Domain\OrderBook\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * OrderBookEntry
     *
     * @see OrderBookEntryTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                   getId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                   hasId()
     * @method bool                                                   isIdFilled()
     * @method bool                                                   isIdChanged()
     * @method \string                                                getUfBybitOrderId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfBybitOrderId(\Bitrix\Main\DB\SqlExpression|\string $ufBybitOrderId)
     * @method bool                                                   hasUfBybitOrderId()
     * @method bool                                                   isUfBybitOrderIdFilled()
     * @method bool                                                   isUfBybitOrderIdChanged()
     * @method \string                                                remindActualUfBybitOrderId()
     * @method \string                                                requireUfBybitOrderId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfBybitOrderId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfBybitOrderId()
     * @method \string                                                fillUfBybitOrderId()
     * @method \int                                                   getUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfCurrencyPairId(\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyPairId)
     * @method bool                                                   hasUfCurrencyPairId()
     * @method bool                                                   isUfCurrencyPairIdFilled()
     * @method bool                                                   isUfCurrencyPairIdChanged()
     * @method \int                                                   remindActualUfCurrencyPairId()
     * @method \int                                                   requireUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfCurrencyPairId()
     * @method \int                                                   fillUfCurrencyPairId()
     * @method \string                                                getUfSide()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfSide(\Bitrix\Main\DB\SqlExpression|\string $ufSide)
     * @method bool                                                   hasUfSide()
     * @method bool                                                   isUfSideFilled()
     * @method bool                                                   isUfSideChanged()
     * @method \string                                                remindActualUfSide()
     * @method \string                                                requireUfSide()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfSide()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfSide()
     * @method \string                                                fillUfSide()
     * @method \float                                                 getUfPrice()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfPrice(\Bitrix\Main\DB\SqlExpression|\float $ufPrice)
     * @method bool                                                   hasUfPrice()
     * @method bool                                                   isUfPriceFilled()
     * @method bool                                                   isUfPriceChanged()
     * @method \float                                                 remindActualUfPrice()
     * @method \float                                                 requireUfPrice()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfPrice()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfPrice()
     * @method \float                                                 fillUfPrice()
     * @method \float                                                 getUfQuantity()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfQuantity(\Bitrix\Main\DB\SqlExpression|\float $ufQuantity)
     * @method bool                                                   hasUfQuantity()
     * @method bool                                                   isUfQuantityFilled()
     * @method bool                                                   isUfQuantityChanged()
     * @method \float                                                 remindActualUfQuantity()
     * @method \float                                                 requireUfQuantity()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfQuantity()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfQuantity()
     * @method \float                                                 fillUfQuantity()
     * @method \float                                                 getUfMinAmount()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfMinAmount(\Bitrix\Main\DB\SqlExpression|\float $ufMinAmount)
     * @method bool                                                   hasUfMinAmount()
     * @method bool                                                   isUfMinAmountFilled()
     * @method bool                                                   isUfMinAmountChanged()
     * @method \float                                                 remindActualUfMinAmount()
     * @method \float                                                 requireUfMinAmount()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfMinAmount()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfMinAmount()
     * @method \float                                                 fillUfMinAmount()
     * @method \float                                                 getUfMaxAmount()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfMaxAmount(\Bitrix\Main\DB\SqlExpression|\float $ufMaxAmount)
     * @method bool                                                   hasUfMaxAmount()
     * @method bool                                                   isUfMaxAmountFilled()
     * @method bool                                                   isUfMaxAmountChanged()
     * @method \float                                                 remindActualUfMaxAmount()
     * @method \float                                                 requireUfMaxAmount()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfMaxAmount()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfMaxAmount()
     * @method \float                                                 fillUfMaxAmount()
     * @method \string                                                getUfCounterpartyName()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfCounterpartyName(\Bitrix\Main\DB\SqlExpression|\string $ufCounterpartyName)
     * @method bool                                                   hasUfCounterpartyName()
     * @method bool                                                   isUfCounterpartyNameFilled()
     * @method bool                                                   isUfCounterpartyNameChanged()
     * @method \string                                                remindActualUfCounterpartyName()
     * @method \string                                                requireUfCounterpartyName()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfCounterpartyName()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfCounterpartyName()
     * @method \string                                                fillUfCounterpartyName()
     * @method \float                                                 getUfCounterpartyRating()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfCounterpartyRating(\Bitrix\Main\DB\SqlExpression|\float $ufCounterpartyRating)
     * @method bool                                                   hasUfCounterpartyRating()
     * @method bool                                                   isUfCounterpartyRatingFilled()
     * @method bool                                                   isUfCounterpartyRatingChanged()
     * @method \float                                                 remindActualUfCounterpartyRating()
     * @method \float                                                 requireUfCounterpartyRating()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfCounterpartyRating()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfCounterpartyRating()
     * @method \float                                                 fillUfCounterpartyRating()
     * @method \int                                                   getUfCounterpartyTrades()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfCounterpartyTrades(\Bitrix\Main\DB\SqlExpression|\int $ufCounterpartyTrades)
     * @method bool                                                   hasUfCounterpartyTrades()
     * @method bool                                                   isUfCounterpartyTradesFilled()
     * @method bool                                                   isUfCounterpartyTradesChanged()
     * @method \int                                                   remindActualUfCounterpartyTrades()
     * @method \int                                                   requireUfCounterpartyTrades()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfCounterpartyTrades()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfCounterpartyTrades()
     * @method \int                                                   fillUfCounterpartyTrades()
     * @method \float                                                 getUfCounterpartyCompletionRate()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfCounterpartyCompletionRate(\Bitrix\Main\DB\SqlExpression|\float $ufCounterpartyCompletionRate)
     * @method bool                                                   hasUfCounterpartyCompletionRate()
     * @method bool                                                   isUfCounterpartyCompletionRateFilled()
     * @method bool                                                   isUfCounterpartyCompletionRateChanged()
     * @method \float                                                 remindActualUfCounterpartyCompletionRate()
     * @method \float                                                 requireUfCounterpartyCompletionRate()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfCounterpartyCompletionRate()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfCounterpartyCompletionRate()
     * @method \float                                                 fillUfCounterpartyCompletionRate()
     * @method \string                                                getUfPaymentMethodIds()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfPaymentMethodIds(\Bitrix\Main\DB\SqlExpression|\string $ufPaymentMethodIds)
     * @method bool                                                   hasUfPaymentMethodIds()
     * @method bool                                                   isUfPaymentMethodIdsFilled()
     * @method bool                                                   isUfPaymentMethodIdsChanged()
     * @method \string                                                remindActualUfPaymentMethodIds()
     * @method \string                                                requireUfPaymentMethodIds()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfPaymentMethodIds()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfPaymentMethodIds()
     * @method \string                                                fillUfPaymentMethodIds()
     * @method \int                                                   getUfPaymentTimeLimit()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfPaymentTimeLimit(\Bitrix\Main\DB\SqlExpression|\int $ufPaymentTimeLimit)
     * @method bool                                                   hasUfPaymentTimeLimit()
     * @method bool                                                   isUfPaymentTimeLimitFilled()
     * @method bool                                                   isUfPaymentTimeLimitChanged()
     * @method \int                                                   remindActualUfPaymentTimeLimit()
     * @method \int                                                   requireUfPaymentTimeLimit()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfPaymentTimeLimit()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfPaymentTimeLimit()
     * @method \int                                                   fillUfPaymentTimeLimit()
     * @method \Bitrix\Main\Type\DateTime                             getUfSyncedAt()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry setUfSyncedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufSyncedAt)
     * @method bool                                                   hasUfSyncedAt()
     * @method bool                                                   isUfSyncedAtFilled()
     * @method bool                                                   isUfSyncedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                             remindActualUfSyncedAt()
     * @method \Bitrix\Main\Type\DateTime                             requireUfSyncedAt()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry resetUfSyncedAt()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry unsetUfSyncedAt()
     * @method \Bitrix\Main\Type\DateTime                             fillUfSyncedAt()
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
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry                                          set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry                                          reset($fieldName)
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry                                          unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry                                          wakeUp($data)
     */
    class EO_OrderBookEntry
    {
        // @var \Rebit\Exchange\Domain\OrderBook\Entity\Table\OrderBookEntryTable
        public static $dataClass = '\Rebit\Exchange\Domain\OrderBook\Entity\Table\OrderBookEntryTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\OrderBook\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * OrderBookEntryCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \string[]                    getUfBybitOrderIdList()
     * @method \string[]                    fillUfBybitOrderId()
     * @method \int[]                       getUfCurrencyPairIdList()
     * @method \int[]                       fillUfCurrencyPairId()
     * @method \string[]                    getUfSideList()
     * @method \string[]                    fillUfSide()
     * @method \float[]                     getUfPriceList()
     * @method \float[]                     fillUfPrice()
     * @method \float[]                     getUfQuantityList()
     * @method \float[]                     fillUfQuantity()
     * @method \float[]                     getUfMinAmountList()
     * @method \float[]                     fillUfMinAmount()
     * @method \float[]                     getUfMaxAmountList()
     * @method \float[]                     fillUfMaxAmount()
     * @method \string[]                    getUfCounterpartyNameList()
     * @method \string[]                    fillUfCounterpartyName()
     * @method \float[]                     getUfCounterpartyRatingList()
     * @method \float[]                     fillUfCounterpartyRating()
     * @method \int[]                       getUfCounterpartyTradesList()
     * @method \int[]                       fillUfCounterpartyTrades()
     * @method \float[]                     getUfCounterpartyCompletionRateList()
     * @method \float[]                     fillUfCounterpartyCompletionRate()
     * @method \string[]                    getUfPaymentMethodIdsList()
     * @method \string[]                    fillUfPaymentMethodIds()
     * @method \int[]                       getUfPaymentTimeLimitList()
     * @method \int[]                       fillUfPaymentTimeLimit()
     * @method \Bitrix\Main\Type\DateTime[] getUfSyncedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfSyncedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                             add(\Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry $object)
     * @method        bool                                                             has(\Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry $object)
     * @method        bool                                                             hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry[]         getAll()
     * @method        bool                                                             remove(\Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry $object)
     * @method        void                                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection                 fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                     save($ignoreEvents = false)
     * @method        void                                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                             rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           current()                                                                                                                                                      Iterator
     * @method        mixed                                                            key()                                                                                                                                                          Iterator
     * @method        void                                                             next()                                                                                                                                                         Iterator
     * @method        bool                                                             valid()                                                                                                                                                        Iterator
     * @method        int                                                              count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection merge(?\Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection $collection)
     * @method        bool                                                             isEmpty()
     * @method        array                                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_OrderBookEntry_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\OrderBook\Entity\Table\OrderBookEntryTable
        public static $dataClass = '\Rebit\Exchange\Domain\OrderBook\Entity\Table\OrderBookEntryTable';
    }
}

namespace Rebit\Exchange\Domain\OrderBook\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_OrderBookEntry_Query                                          query()
     * @method static EO_OrderBookEntry_Result                                         getByPrimary($primary, array $parameters = [])
     * @method static EO_OrderBookEntry_Result                                         getById($id)
     * @method static EO_OrderBookEntry_Result                                         getList(array $parameters = [])
     * @method static EO_OrderBookEntry_Entity                                         getEntity()
     * @method static \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection createCollection()
     * @method static \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection wakeUpCollection($rows)
     */
    class OrderBookEntryTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_OrderBookEntry_Result                                         exec()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           fetchObject()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection fetchCollection()
     */
    class EO_OrderBookEntry_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           fetchObject()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection fetchCollection()
     */
    class EO_OrderBookEntry_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection createCollection()
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection wakeUpCollection($rows)
     */
    class EO_OrderBookEntry_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable

namespace Rebit\Exchange\Domain\Trade\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * Trade
     *
     * @see TradeTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                      getId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                      hasId()
     * @method bool                                      isIdFilled()
     * @method bool                                      isIdChanged()
     * @method \string                                   getUfBybitOrderId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfBybitOrderId(\Bitrix\Main\DB\SqlExpression|\string $ufBybitOrderId)
     * @method bool                                      hasUfBybitOrderId()
     * @method bool                                      isUfBybitOrderIdFilled()
     * @method bool                                      isUfBybitOrderIdChanged()
     * @method \string                                   remindActualUfBybitOrderId()
     * @method \string                                   requireUfBybitOrderId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfBybitOrderId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfBybitOrderId()
     * @method \string                                   fillUfBybitOrderId()
     * @method \int                                      getUfBybitStatus()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfBybitStatus(\Bitrix\Main\DB\SqlExpression|\int $ufBybitStatus)
     * @method bool                                      hasUfBybitStatus()
     * @method bool                                      isUfBybitStatusFilled()
     * @method bool                                      isUfBybitStatusChanged()
     * @method \int                                      remindActualUfBybitStatus()
     * @method \int                                      requireUfBybitStatus()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfBybitStatus()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfBybitStatus()
     * @method \int                                      fillUfBybitStatus()
     * @method \int                                      getUfBuyerUserId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfBuyerUserId(\Bitrix\Main\DB\SqlExpression|\int $ufBuyerUserId)
     * @method bool                                      hasUfBuyerUserId()
     * @method bool                                      isUfBuyerUserIdFilled()
     * @method bool                                      isUfBuyerUserIdChanged()
     * @method \int                                      remindActualUfBuyerUserId()
     * @method \int                                      requireUfBuyerUserId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfBuyerUserId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfBuyerUserId()
     * @method \int                                      fillUfBuyerUserId()
     * @method \int                                      getUfSellerUserId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfSellerUserId(\Bitrix\Main\DB\SqlExpression|\int $ufSellerUserId)
     * @method bool                                      hasUfSellerUserId()
     * @method bool                                      isUfSellerUserIdFilled()
     * @method bool                                      isUfSellerUserIdChanged()
     * @method \int                                      remindActualUfSellerUserId()
     * @method \int                                      requireUfSellerUserId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfSellerUserId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfSellerUserId()
     * @method \int                                      fillUfSellerUserId()
     * @method \int                                      getUfAdvertisementId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfAdvertisementId(\Bitrix\Main\DB\SqlExpression|\int $ufAdvertisementId)
     * @method bool                                      hasUfAdvertisementId()
     * @method bool                                      isUfAdvertisementIdFilled()
     * @method bool                                      isUfAdvertisementIdChanged()
     * @method \int                                      remindActualUfAdvertisementId()
     * @method \int                                      requireUfAdvertisementId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfAdvertisementId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfAdvertisementId()
     * @method \int                                      fillUfAdvertisementId()
     * @method \int                                      getUfOrderBookEntryId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfOrderBookEntryId(\Bitrix\Main\DB\SqlExpression|\int $ufOrderBookEntryId)
     * @method bool                                      hasUfOrderBookEntryId()
     * @method bool                                      isUfOrderBookEntryIdFilled()
     * @method bool                                      isUfOrderBookEntryIdChanged()
     * @method \int                                      remindActualUfOrderBookEntryId()
     * @method \int                                      requireUfOrderBookEntryId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfOrderBookEntryId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfOrderBookEntryId()
     * @method \int                                      fillUfOrderBookEntryId()
     * @method \int                                      getUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfCurrencyPairId(\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyPairId)
     * @method bool                                      hasUfCurrencyPairId()
     * @method bool                                      isUfCurrencyPairIdFilled()
     * @method bool                                      isUfCurrencyPairIdChanged()
     * @method \int                                      remindActualUfCurrencyPairId()
     * @method \int                                      requireUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfCurrencyPairId()
     * @method \int                                      fillUfCurrencyPairId()
     * @method \string                                   getUfSide()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfSide(\Bitrix\Main\DB\SqlExpression|\string $ufSide)
     * @method bool                                      hasUfSide()
     * @method bool                                      isUfSideFilled()
     * @method bool                                      isUfSideChanged()
     * @method \string                                   remindActualUfSide()
     * @method \string                                   requireUfSide()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfSide()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfSide()
     * @method \string                                   fillUfSide()
     * @method \float                                    getUfPrice()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfPrice(\Bitrix\Main\DB\SqlExpression|\float $ufPrice)
     * @method bool                                      hasUfPrice()
     * @method bool                                      isUfPriceFilled()
     * @method bool                                      isUfPriceChanged()
     * @method \float                                    remindActualUfPrice()
     * @method \float                                    requireUfPrice()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfPrice()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfPrice()
     * @method \float                                    fillUfPrice()
     * @method \float                                    getUfQuantity()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfQuantity(\Bitrix\Main\DB\SqlExpression|\float $ufQuantity)
     * @method bool                                      hasUfQuantity()
     * @method bool                                      isUfQuantityFilled()
     * @method bool                                      isUfQuantityChanged()
     * @method \float                                    remindActualUfQuantity()
     * @method \float                                    requireUfQuantity()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfQuantity()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfQuantity()
     * @method \float                                    fillUfQuantity()
     * @method \float                                    getUfFiatAmount()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfFiatAmount(\Bitrix\Main\DB\SqlExpression|\float $ufFiatAmount)
     * @method bool                                      hasUfFiatAmount()
     * @method bool                                      isUfFiatAmountFilled()
     * @method bool                                      isUfFiatAmountChanged()
     * @method \float                                    remindActualUfFiatAmount()
     * @method \float                                    requireUfFiatAmount()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfFiatAmount()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfFiatAmount()
     * @method \float                                    fillUfFiatAmount()
     * @method \float                                    getUfFee()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfFee(\Bitrix\Main\DB\SqlExpression|\float $ufFee)
     * @method bool                                      hasUfFee()
     * @method bool                                      isUfFeeFilled()
     * @method bool                                      isUfFeeChanged()
     * @method \float                                    remindActualUfFee()
     * @method \float                                    requireUfFee()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfFee()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfFee()
     * @method \float                                    fillUfFee()
     * @method \int                                      getUfPaymentMethodId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfPaymentMethodId(\Bitrix\Main\DB\SqlExpression|\int $ufPaymentMethodId)
     * @method bool                                      hasUfPaymentMethodId()
     * @method bool                                      isUfPaymentMethodIdFilled()
     * @method bool                                      isUfPaymentMethodIdChanged()
     * @method \int                                      remindActualUfPaymentMethodId()
     * @method \int                                      requireUfPaymentMethodId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfPaymentMethodId()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfPaymentMethodId()
     * @method \int                                      fillUfPaymentMethodId()
     * @method \string                                   getUfPaymentDetails()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfPaymentDetails(\Bitrix\Main\DB\SqlExpression|\string $ufPaymentDetails)
     * @method bool                                      hasUfPaymentDetails()
     * @method bool                                      isUfPaymentDetailsFilled()
     * @method bool                                      isUfPaymentDetailsChanged()
     * @method \string                                   remindActualUfPaymentDetails()
     * @method \string                                   requireUfPaymentDetails()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfPaymentDetails()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfPaymentDetails()
     * @method \string                                   fillUfPaymentDetails()
     * @method \string                                   getUfComment()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfComment(\Bitrix\Main\DB\SqlExpression|\string $ufComment)
     * @method bool                                      hasUfComment()
     * @method bool                                      isUfCommentFilled()
     * @method bool                                      isUfCommentChanged()
     * @method \string                                   remindActualUfComment()
     * @method \string                                   requireUfComment()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfComment()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfComment()
     * @method \string                                   fillUfComment()
     * @method \string                                   getUfStatus()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfStatus(\Bitrix\Main\DB\SqlExpression|\string $ufStatus)
     * @method bool                                      hasUfStatus()
     * @method bool                                      isUfStatusFilled()
     * @method bool                                      isUfStatusChanged()
     * @method \string                                   remindActualUfStatus()
     * @method \string                                   requireUfStatus()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfStatus()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfStatus()
     * @method \string                                   fillUfStatus()
     * @method \Bitrix\Main\Type\DateTime                getUfPaymentDeadline()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfPaymentDeadline(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufPaymentDeadline)
     * @method bool                                      hasUfPaymentDeadline()
     * @method bool                                      isUfPaymentDeadlineFilled()
     * @method bool                                      isUfPaymentDeadlineChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfPaymentDeadline()
     * @method \Bitrix\Main\Type\DateTime                requireUfPaymentDeadline()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfPaymentDeadline()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfPaymentDeadline()
     * @method \Bitrix\Main\Type\DateTime                fillUfPaymentDeadline()
     * @method \Bitrix\Main\Type\DateTime                getUfPaidAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfPaidAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufPaidAt)
     * @method bool                                      hasUfPaidAt()
     * @method bool                                      isUfPaidAtFilled()
     * @method bool                                      isUfPaidAtChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfPaidAt()
     * @method \Bitrix\Main\Type\DateTime                requireUfPaidAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfPaidAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfPaidAt()
     * @method \Bitrix\Main\Type\DateTime                fillUfPaidAt()
     * @method \Bitrix\Main\Type\DateTime                getUfConfirmedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfConfirmedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufConfirmedAt)
     * @method bool                                      hasUfConfirmedAt()
     * @method bool                                      isUfConfirmedAtFilled()
     * @method bool                                      isUfConfirmedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfConfirmedAt()
     * @method \Bitrix\Main\Type\DateTime                requireUfConfirmedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfConfirmedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfConfirmedAt()
     * @method \Bitrix\Main\Type\DateTime                fillUfConfirmedAt()
     * @method \Bitrix\Main\Type\DateTime                getUfCompletedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfCompletedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCompletedAt)
     * @method bool                                      hasUfCompletedAt()
     * @method bool                                      isUfCompletedAtFilled()
     * @method bool                                      isUfCompletedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfCompletedAt()
     * @method \Bitrix\Main\Type\DateTime                requireUfCompletedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfCompletedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfCompletedAt()
     * @method \Bitrix\Main\Type\DateTime                fillUfCompletedAt()
     * @method \Bitrix\Main\Type\DateTime                getUfCancelledAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfCancelledAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCancelledAt)
     * @method bool                                      hasUfCancelledAt()
     * @method bool                                      isUfCancelledAtFilled()
     * @method bool                                      isUfCancelledAtChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfCancelledAt()
     * @method \Bitrix\Main\Type\DateTime                requireUfCancelledAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfCancelledAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfCancelledAt()
     * @method \Bitrix\Main\Type\DateTime                fillUfCancelledAt()
     * @method \string                                   getUfCancelReason()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfCancelReason(\Bitrix\Main\DB\SqlExpression|\string $ufCancelReason)
     * @method bool                                      hasUfCancelReason()
     * @method bool                                      isUfCancelReasonFilled()
     * @method bool                                      isUfCancelReasonChanged()
     * @method \string                                   remindActualUfCancelReason()
     * @method \string                                   requireUfCancelReason()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfCancelReason()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfCancelReason()
     * @method \string                                   fillUfCancelReason()
     * @method \string                                   getUfCounterpartyName()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfCounterpartyName(\Bitrix\Main\DB\SqlExpression|\string $ufCounterpartyName)
     * @method bool                                      hasUfCounterpartyName()
     * @method bool                                      isUfCounterpartyNameFilled()
     * @method bool                                      isUfCounterpartyNameChanged()
     * @method \string                                   remindActualUfCounterpartyName()
     * @method \string                                   requireUfCounterpartyName()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfCounterpartyName()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfCounterpartyName()
     * @method \string                                   fillUfCounterpartyName()
     * @method \Bitrix\Main\Type\DateTime                getUfCreatedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfCreatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                                      hasUfCreatedAt()
     * @method bool                                      isUfCreatedAtFilled()
     * @method bool                                      isUfCreatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                requireUfCreatedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfCreatedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                fillUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                getUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade setUfUpdatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                                      hasUfUpdatedAt()
     * @method bool                                      isUfUpdatedAtFilled()
     * @method bool                                      isUfUpdatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                remindActualUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                requireUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade resetUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade unsetUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                fillUfUpdatedAt()
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
     * @method        \Rebit\Exchange\Domain\Trade\Entity\Trade                                                       set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\Trade\Entity\Trade                                                       reset($fieldName)
     * @method        \Rebit\Exchange\Domain\Trade\Entity\Trade                                                       unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\Trade\Entity\Trade                                                       wakeUp($data)
     */
    class EO_Trade
    {
        // @var \Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable
        public static $dataClass = '\Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\Trade\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * TradeCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \string[]                    getUfBybitOrderIdList()
     * @method \string[]                    fillUfBybitOrderId()
     * @method \int[]                       getUfBybitStatusList()
     * @method \int[]                       fillUfBybitStatus()
     * @method \int[]                       getUfBuyerUserIdList()
     * @method \int[]                       fillUfBuyerUserId()
     * @method \int[]                       getUfSellerUserIdList()
     * @method \int[]                       fillUfSellerUserId()
     * @method \int[]                       getUfAdvertisementIdList()
     * @method \int[]                       fillUfAdvertisementId()
     * @method \int[]                       getUfOrderBookEntryIdList()
     * @method \int[]                       fillUfOrderBookEntryId()
     * @method \int[]                       getUfCurrencyPairIdList()
     * @method \int[]                       fillUfCurrencyPairId()
     * @method \string[]                    getUfSideList()
     * @method \string[]                    fillUfSide()
     * @method \float[]                     getUfPriceList()
     * @method \float[]                     fillUfPrice()
     * @method \float[]                     getUfQuantityList()
     * @method \float[]                     fillUfQuantity()
     * @method \float[]                     getUfFiatAmountList()
     * @method \float[]                     fillUfFiatAmount()
     * @method \float[]                     getUfFeeList()
     * @method \float[]                     fillUfFee()
     * @method \int[]                       getUfPaymentMethodIdList()
     * @method \int[]                       fillUfPaymentMethodId()
     * @method \string[]                    getUfPaymentDetailsList()
     * @method \string[]                    fillUfPaymentDetails()
     * @method \string[]                    getUfCommentList()
     * @method \string[]                    fillUfComment()
     * @method \string[]                    getUfStatusList()
     * @method \string[]                    fillUfStatus()
     * @method \Bitrix\Main\Type\DateTime[] getUfPaymentDeadlineList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfPaymentDeadline()
     * @method \Bitrix\Main\Type\DateTime[] getUfPaidAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfPaidAt()
     * @method \Bitrix\Main\Type\DateTime[] getUfConfirmedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfConfirmedAt()
     * @method \Bitrix\Main\Type\DateTime[] getUfCompletedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfCompletedAt()
     * @method \Bitrix\Main\Type\DateTime[] getUfCancelledAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfCancelledAt()
     * @method \string[]                    getUfCancelReasonList()
     * @method \string[]                    fillUfCancelReason()
     * @method \string[]                    getUfCounterpartyNameList()
     * @method \string[]                    fillUfCounterpartyName()
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
     * @method        void                                                add(\Rebit\Exchange\Domain\Trade\Entity\Trade $object)
     * @method        bool                                                has(\Rebit\Exchange\Domain\Trade\Entity\Trade $object)
     * @method        bool                                                hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Trade\Entity\Trade           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Trade\Entity\Trade[]         getAll()
     * @method        bool                                                remove(\Rebit\Exchange\Domain\Trade\Entity\Trade $object)
     * @method        void                                                removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection    fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\Trade\Entity\TradeCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                        save($ignoreEvents = false)
     * @method        void                                                offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\Trade\Entity\Trade           current()                                                                                                                                                      Iterator
     * @method        mixed                                               key()                                                                                                                                                          Iterator
     * @method        void                                                next()                                                                                                                                                         Iterator
     * @method        bool                                                valid()                                                                                                                                                        Iterator
     * @method        int                                                 count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\Trade\Entity\TradeCollection merge(?\Rebit\Exchange\Domain\Trade\Entity\TradeCollection $collection)
     * @method        bool                                                isEmpty()
     * @method        array                                               collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_Trade_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable
        public static $dataClass = '\Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable';
    }
}

namespace Rebit\Exchange\Domain\Trade\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_Trade_Query                                      query()
     * @method static EO_Trade_Result                                     getByPrimary($primary, array $parameters = [])
     * @method static EO_Trade_Result                                     getById($id)
     * @method static EO_Trade_Result                                     getList(array $parameters = [])
     * @method static EO_Trade_Entity                                     getEntity()
     * @method static \Rebit\Exchange\Domain\Trade\Entity\Trade           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\Trade\Entity\TradeCollection createCollection()
     * @method static \Rebit\Exchange\Domain\Trade\Entity\Trade           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\Trade\Entity\TradeCollection wakeUpCollection($rows)
     */
    class TradeTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_Trade_Result                                     exec()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade           fetchObject()
     * @method \Rebit\Exchange\Domain\Trade\Entity\TradeCollection fetchCollection()
     */
    class EO_Trade_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade           fetchObject()
     * @method \Rebit\Exchange\Domain\Trade\Entity\TradeCollection fetchCollection()
     */
    class EO_Trade_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\Trade\Entity\TradeCollection createCollection()
     * @method \Rebit\Exchange\Domain\Trade\Entity\Trade           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\Trade\Entity\TradeCollection wakeUpCollection($rows)
     */
    class EO_Trade_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\TradeChat\Entity\Table\TradeMessageTable

namespace Rebit\Exchange\Domain\TradeChat\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * TradeMessage
     *
     * @see TradeMessageTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                 getId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                 hasId()
     * @method bool                                                 isIdFilled()
     * @method bool                                                 isIdChanged()
     * @method \int                                                 getUfTradeId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfTradeId(\Bitrix\Main\DB\SqlExpression|\int $ufTradeId)
     * @method bool                                                 hasUfTradeId()
     * @method bool                                                 isUfTradeIdFilled()
     * @method bool                                                 isUfTradeIdChanged()
     * @method \int                                                 remindActualUfTradeId()
     * @method \int                                                 requireUfTradeId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfTradeId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfTradeId()
     * @method \int                                                 fillUfTradeId()
     * @method \int                                                 getUfUserId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfUserId(\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                                                 hasUfUserId()
     * @method bool                                                 isUfUserIdFilled()
     * @method bool                                                 isUfUserIdChanged()
     * @method \int                                                 remindActualUfUserId()
     * @method \int                                                 requireUfUserId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfUserId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfUserId()
     * @method \int                                                 fillUfUserId()
     * @method \string                                              getUfMessage()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfMessage(\Bitrix\Main\DB\SqlExpression|\string $ufMessage)
     * @method bool                                                 hasUfMessage()
     * @method bool                                                 isUfMessageFilled()
     * @method bool                                                 isUfMessageChanged()
     * @method \string                                              remindActualUfMessage()
     * @method \string                                              requireUfMessage()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfMessage()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfMessage()
     * @method \string                                              fillUfMessage()
     * @method \string                                              getUfMessageType()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfMessageType(\Bitrix\Main\DB\SqlExpression|\string $ufMessageType)
     * @method bool                                                 hasUfMessageType()
     * @method bool                                                 isUfMessageTypeFilled()
     * @method bool                                                 isUfMessageTypeChanged()
     * @method \string                                              remindActualUfMessageType()
     * @method \string                                              requireUfMessageType()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfMessageType()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfMessageType()
     * @method \string                                              fillUfMessageType()
     * @method \string                                              getUfContentType()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfContentType(\Bitrix\Main\DB\SqlExpression|\string $ufContentType)
     * @method bool                                                 hasUfContentType()
     * @method bool                                                 isUfContentTypeFilled()
     * @method bool                                                 isUfContentTypeChanged()
     * @method \string                                              remindActualUfContentType()
     * @method \string                                              requireUfContentType()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfContentType()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfContentType()
     * @method \string                                              fillUfContentType()
     * @method \string                                              getUfBybitMsgUuid()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfBybitMsgUuid(\Bitrix\Main\DB\SqlExpression|\string $ufBybitMsgUuid)
     * @method bool                                                 hasUfBybitMsgUuid()
     * @method bool                                                 isUfBybitMsgUuidFilled()
     * @method bool                                                 isUfBybitMsgUuidChanged()
     * @method \string                                              remindActualUfBybitMsgUuid()
     * @method \string                                              requireUfBybitMsgUuid()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfBybitMsgUuid()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfBybitMsgUuid()
     * @method \string                                              fillUfBybitMsgUuid()
     * @method \string                                              getUfFileName()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfFileName(\Bitrix\Main\DB\SqlExpression|\string $ufFileName)
     * @method bool                                                 hasUfFileName()
     * @method bool                                                 isUfFileNameFilled()
     * @method bool                                                 isUfFileNameChanged()
     * @method \string                                              remindActualUfFileName()
     * @method \string                                              requireUfFileName()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfFileName()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfFileName()
     * @method \string                                              fillUfFileName()
     * @method \int                                                 getUfScriptStepId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfScriptStepId(\Bitrix\Main\DB\SqlExpression|\int $ufScriptStepId)
     * @method bool                                                 hasUfScriptStepId()
     * @method bool                                                 isUfScriptStepIdFilled()
     * @method bool                                                 isUfScriptStepIdChanged()
     * @method \int                                                 remindActualUfScriptStepId()
     * @method \int                                                 requireUfScriptStepId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfScriptStepId()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfScriptStepId()
     * @method \int                                                 fillUfScriptStepId()
     * @method \boolean                                             getUfIsRead()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfIsRead(\Bitrix\Main\DB\SqlExpression|\boolean $ufIsRead)
     * @method bool                                                 hasUfIsRead()
     * @method bool                                                 isUfIsReadFilled()
     * @method bool                                                 isUfIsReadChanged()
     * @method \boolean                                             remindActualUfIsRead()
     * @method \boolean                                             requireUfIsRead()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfIsRead()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfIsRead()
     * @method \boolean                                             fillUfIsRead()
     * @method \Bitrix\Main\Type\DateTime                           getUfCreatedAt()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage setUfCreatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                                                 hasUfCreatedAt()
     * @method bool                                                 isUfCreatedAtFilled()
     * @method bool                                                 isUfCreatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                           remindActualUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                           requireUfCreatedAt()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage resetUfCreatedAt()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage unsetUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                           fillUfCreatedAt()
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
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage                                            set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage                                            reset($fieldName)
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage                                            unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage                                            wakeUp($data)
     */
    class EO_TradeMessage
    {
        // @var \Rebit\Exchange\Domain\TradeChat\Entity\Table\TradeMessageTable
        public static $dataClass = '\Rebit\Exchange\Domain\TradeChat\Entity\Table\TradeMessageTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\TradeChat\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * TradeMessageCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \int[]                       getUfTradeIdList()
     * @method \int[]                       fillUfTradeId()
     * @method \int[]                       getUfUserIdList()
     * @method \int[]                       fillUfUserId()
     * @method \string[]                    getUfMessageList()
     * @method \string[]                    fillUfMessage()
     * @method \string[]                    getUfMessageTypeList()
     * @method \string[]                    fillUfMessageType()
     * @method \string[]                    getUfContentTypeList()
     * @method \string[]                    fillUfContentType()
     * @method \string[]                    getUfBybitMsgUuidList()
     * @method \string[]                    fillUfBybitMsgUuid()
     * @method \string[]                    getUfFileNameList()
     * @method \string[]                    fillUfFileName()
     * @method \int[]                       getUfScriptStepIdList()
     * @method \int[]                       fillUfScriptStepId()
     * @method \boolean[]                   getUfIsReadList()
     * @method \boolean[]                   fillUfIsRead()
     * @method \Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                           add(\Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage $object)
     * @method        bool                                                           has(\Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage $object)
     * @method        bool                                                           hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage[]         getAll()
     * @method        bool                                                           remove(\Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage $object)
     * @method        void                                                           removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection               fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                   save($ignoreEvents = false)
     * @method        void                                                           offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                           offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                           offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                           offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                           rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           current()                                                                                                                                                      Iterator
     * @method        mixed                                                          key()                                                                                                                                                          Iterator
     * @method        void                                                           next()                                                                                                                                                         Iterator
     * @method        bool                                                           valid()                                                                                                                                                        Iterator
     * @method        int                                                            count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection merge(?\Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection $collection)
     * @method        bool                                                           isEmpty()
     * @method        array                                                          collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_TradeMessage_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\TradeChat\Entity\Table\TradeMessageTable
        public static $dataClass = '\Rebit\Exchange\Domain\TradeChat\Entity\Table\TradeMessageTable';
    }
}

namespace Rebit\Exchange\Domain\TradeChat\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_TradeMessage_Query                                          query()
     * @method static EO_TradeMessage_Result                                         getByPrimary($primary, array $parameters = [])
     * @method static EO_TradeMessage_Result                                         getById($id)
     * @method static EO_TradeMessage_Result                                         getList(array $parameters = [])
     * @method static EO_TradeMessage_Entity                                         getEntity()
     * @method static \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection createCollection()
     * @method static \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection wakeUpCollection($rows)
     */
    class TradeMessageTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_TradeMessage_Result                                         exec()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           fetchObject()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection fetchCollection()
     */
    class EO_TradeMessage_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           fetchObject()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection fetchCollection()
     */
    class EO_TradeMessage_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection createCollection()
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection wakeUpCollection($rows)
     */
    class EO_TradeMessage_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable

namespace Rebit\Exchange\Domain\PaymentMethod\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * PaymentMethod
     *
     * @see PaymentMethodTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                      getId()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                      hasId()
     * @method bool                                                      isIdFilled()
     * @method bool                                                      isIdChanged()
     * @method \string                                                   getUfCode()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod setUfCode(\Bitrix\Main\DB\SqlExpression|\string $ufCode)
     * @method bool                                                      hasUfCode()
     * @method bool                                                      isUfCodeFilled()
     * @method bool                                                      isUfCodeChanged()
     * @method \string                                                   remindActualUfCode()
     * @method \string                                                   requireUfCode()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod resetUfCode()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod unsetUfCode()
     * @method \string                                                   fillUfCode()
     * @method \string                                                   getUfName()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod setUfName(\Bitrix\Main\DB\SqlExpression|\string $ufName)
     * @method bool                                                      hasUfName()
     * @method bool                                                      isUfNameFilled()
     * @method bool                                                      isUfNameChanged()
     * @method \string                                                   remindActualUfName()
     * @method \string                                                   requireUfName()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod resetUfName()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod unsetUfName()
     * @method \string                                                   fillUfName()
     * @method \int                                                      getUfIcon()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod setUfIcon(\Bitrix\Main\DB\SqlExpression|\int $ufIcon)
     * @method bool                                                      hasUfIcon()
     * @method bool                                                      isUfIconFilled()
     * @method bool                                                      isUfIconChanged()
     * @method \int                                                      remindActualUfIcon()
     * @method \int                                                      requireUfIcon()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod resetUfIcon()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod unsetUfIcon()
     * @method \int                                                      fillUfIcon()
     * @method \boolean                                                  getUfIsActive()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod setUfIsActive(\Bitrix\Main\DB\SqlExpression|\boolean $ufIsActive)
     * @method bool                                                      hasUfIsActive()
     * @method bool                                                      isUfIsActiveFilled()
     * @method bool                                                      isUfIsActiveChanged()
     * @method \boolean                                                  remindActualUfIsActive()
     * @method \boolean                                                  requireUfIsActive()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod resetUfIsActive()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod unsetUfIsActive()
     * @method \boolean                                                  fillUfIsActive()
     * @method \int                                                      getUfSort()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod setUfSort(\Bitrix\Main\DB\SqlExpression|\int $ufSort)
     * @method bool                                                      hasUfSort()
     * @method bool                                                      isUfSortFilled()
     * @method bool                                                      isUfSortChanged()
     * @method \int                                                      remindActualUfSort()
     * @method \int                                                      requireUfSort()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod resetUfSort()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod unsetUfSort()
     * @method \int                                                      fillUfSort()
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
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod                                       set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod                                       reset($fieldName)
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod                                       unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod                                       wakeUp($data)
     */
    class EO_PaymentMethod
    {
        // @var \Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable
        public static $dataClass = '\Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\PaymentMethod\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * PaymentMethodCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]     getIdList()
     * @method \string[]  getUfCodeList()
     * @method \string[]  fillUfCode()
     * @method \string[]  getUfNameList()
     * @method \string[]  fillUfName()
     * @method \int[]     getUfIconList()
     * @method \int[]     fillUfIcon()
     * @method \boolean[] getUfIsActiveList()
     * @method \boolean[] fillUfIsActive()
     * @method \int[]     getUfSortList()
     * @method \int[]     fillUfSort()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                                add(\Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod $object)
     * @method        bool                                                                has(\Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod $object)
     * @method        bool                                                                hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod[]         getAll()
     * @method        bool                                                                remove(\Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod $object)
     * @method        void                                                                removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection                    fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                        save($ignoreEvents = false)
     * @method        void                                                                offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                                offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                                offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                                offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                                rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           current()                                                                                                                                                      Iterator
     * @method        mixed                                                               key()                                                                                                                                                          Iterator
     * @method        void                                                                next()                                                                                                                                                         Iterator
     * @method        bool                                                                valid()                                                                                                                                                        Iterator
     * @method        int                                                                 count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection merge(?\Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection $collection)
     * @method        bool                                                                isEmpty()
     * @method        array                                                               collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_PaymentMethod_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable
        public static $dataClass = '\Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable';
    }
}

namespace Rebit\Exchange\Domain\PaymentMethod\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_PaymentMethod_Query                                              query()
     * @method static EO_PaymentMethod_Result                                             getByPrimary($primary, array $parameters = [])
     * @method static EO_PaymentMethod_Result                                             getById($id)
     * @method static EO_PaymentMethod_Result                                             getList(array $parameters = [])
     * @method static EO_PaymentMethod_Entity                                             getEntity()
     * @method static \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection createCollection()
     * @method static \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection wakeUpCollection($rows)
     */
    class PaymentMethodTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_PaymentMethod_Result                                             exec()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           fetchObject()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection fetchCollection()
     */
    class EO_PaymentMethod_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           fetchObject()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection fetchCollection()
     */
    class EO_PaymentMethod_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection createCollection()
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection wakeUpCollection($rows)
     */
    class EO_PaymentMethod_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Exchange\Domain\Advertisement\Entity\Table\AdvertisementTable

namespace Rebit\Exchange\Domain\Advertisement\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * Advertisement
     *
     * @see AdvertisementTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                      getId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                      hasId()
     * @method bool                                                      isIdFilled()
     * @method bool                                                      isIdChanged()
     * @method \int                                                      getUfUserId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfUserId(\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                                                      hasUfUserId()
     * @method bool                                                      isUfUserIdFilled()
     * @method bool                                                      isUfUserIdChanged()
     * @method \int                                                      remindActualUfUserId()
     * @method \int                                                      requireUfUserId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfUserId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfUserId()
     * @method \int                                                      fillUfUserId()
     * @method \string                                                   getUfBybitAdId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfBybitAdId(\Bitrix\Main\DB\SqlExpression|\string $ufBybitAdId)
     * @method bool                                                      hasUfBybitAdId()
     * @method bool                                                      isUfBybitAdIdFilled()
     * @method bool                                                      isUfBybitAdIdChanged()
     * @method \string                                                   remindActualUfBybitAdId()
     * @method \string                                                   requireUfBybitAdId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfBybitAdId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfBybitAdId()
     * @method \string                                                   fillUfBybitAdId()
     * @method \int                                                      getUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfCurrencyPairId(\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyPairId)
     * @method bool                                                      hasUfCurrencyPairId()
     * @method bool                                                      isUfCurrencyPairIdFilled()
     * @method bool                                                      isUfCurrencyPairIdChanged()
     * @method \int                                                      remindActualUfCurrencyPairId()
     * @method \int                                                      requireUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfCurrencyPairId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfCurrencyPairId()
     * @method \int                                                      fillUfCurrencyPairId()
     * @method \string                                                   getUfSide()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfSide(\Bitrix\Main\DB\SqlExpression|\string $ufSide)
     * @method bool                                                      hasUfSide()
     * @method bool                                                      isUfSideFilled()
     * @method bool                                                      isUfSideChanged()
     * @method \string                                                   remindActualUfSide()
     * @method \string                                                   requireUfSide()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfSide()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfSide()
     * @method \string                                                   fillUfSide()
     * @method \string                                                   getUfPriceType()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfPriceType(\Bitrix\Main\DB\SqlExpression|\string $ufPriceType)
     * @method bool                                                      hasUfPriceType()
     * @method bool                                                      isUfPriceTypeFilled()
     * @method bool                                                      isUfPriceTypeChanged()
     * @method \string                                                   remindActualUfPriceType()
     * @method \string                                                   requireUfPriceType()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfPriceType()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfPriceType()
     * @method \string                                                   fillUfPriceType()
     * @method \float                                                    getUfPrice()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfPrice(\Bitrix\Main\DB\SqlExpression|\float $ufPrice)
     * @method bool                                                      hasUfPrice()
     * @method bool                                                      isUfPriceFilled()
     * @method bool                                                      isUfPriceChanged()
     * @method \float                                                    remindActualUfPrice()
     * @method \float                                                    requireUfPrice()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfPrice()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfPrice()
     * @method \float                                                    fillUfPrice()
     * @method \float                                                    getUfPremium()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfPremium(\Bitrix\Main\DB\SqlExpression|\float $ufPremium)
     * @method bool                                                      hasUfPremium()
     * @method bool                                                      isUfPremiumFilled()
     * @method bool                                                      isUfPremiumChanged()
     * @method \float                                                    remindActualUfPremium()
     * @method \float                                                    requireUfPremium()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfPremium()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfPremium()
     * @method \float                                                    fillUfPremium()
     * @method \float                                                    getUfQuantity()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfQuantity(\Bitrix\Main\DB\SqlExpression|\float $ufQuantity)
     * @method bool                                                      hasUfQuantity()
     * @method bool                                                      isUfQuantityFilled()
     * @method bool                                                      isUfQuantityChanged()
     * @method \float                                                    remindActualUfQuantity()
     * @method \float                                                    requireUfQuantity()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfQuantity()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfQuantity()
     * @method \float                                                    fillUfQuantity()
     * @method \float                                                    getUfQuantityRemaining()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfQuantityRemaining(\Bitrix\Main\DB\SqlExpression|\float $ufQuantityRemaining)
     * @method bool                                                      hasUfQuantityRemaining()
     * @method bool                                                      isUfQuantityRemainingFilled()
     * @method bool                                                      isUfQuantityRemainingChanged()
     * @method \float                                                    remindActualUfQuantityRemaining()
     * @method \float                                                    requireUfQuantityRemaining()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfQuantityRemaining()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfQuantityRemaining()
     * @method \float                                                    fillUfQuantityRemaining()
     * @method \float                                                    getUfMinAmount()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfMinAmount(\Bitrix\Main\DB\SqlExpression|\float $ufMinAmount)
     * @method bool                                                      hasUfMinAmount()
     * @method bool                                                      isUfMinAmountFilled()
     * @method bool                                                      isUfMinAmountChanged()
     * @method \float                                                    remindActualUfMinAmount()
     * @method \float                                                    requireUfMinAmount()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfMinAmount()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfMinAmount()
     * @method \float                                                    fillUfMinAmount()
     * @method \float                                                    getUfMaxAmount()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfMaxAmount(\Bitrix\Main\DB\SqlExpression|\float $ufMaxAmount)
     * @method bool                                                      hasUfMaxAmount()
     * @method bool                                                      isUfMaxAmountFilled()
     * @method bool                                                      isUfMaxAmountChanged()
     * @method \float                                                    remindActualUfMaxAmount()
     * @method \float                                                    requireUfMaxAmount()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfMaxAmount()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfMaxAmount()
     * @method \float                                                    fillUfMaxAmount()
     * @method \string                                                   getUfPaymentMethodIds()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfPaymentMethodIds(\Bitrix\Main\DB\SqlExpression|\string $ufPaymentMethodIds)
     * @method bool                                                      hasUfPaymentMethodIds()
     * @method bool                                                      isUfPaymentMethodIdsFilled()
     * @method bool                                                      isUfPaymentMethodIdsChanged()
     * @method \string                                                   remindActualUfPaymentMethodIds()
     * @method \string                                                   requireUfPaymentMethodIds()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfPaymentMethodIds()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfPaymentMethodIds()
     * @method \string                                                   fillUfPaymentMethodIds()
     * @method \int                                                      getUfPaymentPeriod()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfPaymentPeriod(\Bitrix\Main\DB\SqlExpression|\int $ufPaymentPeriod)
     * @method bool                                                      hasUfPaymentPeriod()
     * @method bool                                                      isUfPaymentPeriodFilled()
     * @method bool                                                      isUfPaymentPeriodChanged()
     * @method \int                                                      remindActualUfPaymentPeriod()
     * @method \int                                                      requireUfPaymentPeriod()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfPaymentPeriod()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfPaymentPeriod()
     * @method \int                                                      fillUfPaymentPeriod()
     * @method \float                                                    getUfFeeRate()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfFeeRate(\Bitrix\Main\DB\SqlExpression|\float $ufFeeRate)
     * @method bool                                                      hasUfFeeRate()
     * @method bool                                                      isUfFeeRateFilled()
     * @method bool                                                      isUfFeeRateChanged()
     * @method \float                                                    remindActualUfFeeRate()
     * @method \float                                                    requireUfFeeRate()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfFeeRate()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfFeeRate()
     * @method \float                                                    fillUfFeeRate()
     * @method \string                                                   getUfConditions()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfConditions(\Bitrix\Main\DB\SqlExpression|\string $ufConditions)
     * @method bool                                                      hasUfConditions()
     * @method bool                                                      isUfConditionsFilled()
     * @method bool                                                      isUfConditionsChanged()
     * @method \string                                                   remindActualUfConditions()
     * @method \string                                                   requireUfConditions()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfConditions()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfConditions()
     * @method \string                                                   fillUfConditions()
     * @method \int                                                      getUfChatScriptId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfChatScriptId(\Bitrix\Main\DB\SqlExpression|\int $ufChatScriptId)
     * @method bool                                                      hasUfChatScriptId()
     * @method bool                                                      isUfChatScriptIdFilled()
     * @method bool                                                      isUfChatScriptIdChanged()
     * @method \int                                                      remindActualUfChatScriptId()
     * @method \int                                                      requireUfChatScriptId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfChatScriptId()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfChatScriptId()
     * @method \int                                                      fillUfChatScriptId()
     * @method \string                                                   getUfStatus()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfStatus(\Bitrix\Main\DB\SqlExpression|\string $ufStatus)
     * @method bool                                                      hasUfStatus()
     * @method bool                                                      isUfStatusFilled()
     * @method bool                                                      isUfStatusChanged()
     * @method \string                                                   remindActualUfStatus()
     * @method \string                                                   requireUfStatus()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfStatus()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfStatus()
     * @method \string                                                   fillUfStatus()
     * @method \Bitrix\Main\Type\DateTime                                getUfCreatedAt()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfCreatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                                                      hasUfCreatedAt()
     * @method bool                                                      isUfCreatedAtFilled()
     * @method bool                                                      isUfCreatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                                remindActualUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                                requireUfCreatedAt()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfCreatedAt()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                                fillUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                                getUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement setUfUpdatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                                                      hasUfUpdatedAt()
     * @method bool                                                      isUfUpdatedAtFilled()
     * @method bool                                                      isUfUpdatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                                remindActualUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                                requireUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement resetUfUpdatedAt()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement unsetUfUpdatedAt()
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
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement                                       set($fieldName, $value)
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement                                       reset($fieldName)
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement                                       unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement                                       wakeUp($data)
     */
    class EO_Advertisement
    {
        // @var \Rebit\Exchange\Domain\Advertisement\Entity\Table\AdvertisementTable
        public static $dataClass = '\Rebit\Exchange\Domain\Advertisement\Entity\Table\AdvertisementTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Exchange\Domain\Advertisement\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * AdvertisementCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \int[]                       getUfUserIdList()
     * @method \int[]                       fillUfUserId()
     * @method \string[]                    getUfBybitAdIdList()
     * @method \string[]                    fillUfBybitAdId()
     * @method \int[]                       getUfCurrencyPairIdList()
     * @method \int[]                       fillUfCurrencyPairId()
     * @method \string[]                    getUfSideList()
     * @method \string[]                    fillUfSide()
     * @method \string[]                    getUfPriceTypeList()
     * @method \string[]                    fillUfPriceType()
     * @method \float[]                     getUfPriceList()
     * @method \float[]                     fillUfPrice()
     * @method \float[]                     getUfPremiumList()
     * @method \float[]                     fillUfPremium()
     * @method \float[]                     getUfQuantityList()
     * @method \float[]                     fillUfQuantity()
     * @method \float[]                     getUfQuantityRemainingList()
     * @method \float[]                     fillUfQuantityRemaining()
     * @method \float[]                     getUfMinAmountList()
     * @method \float[]                     fillUfMinAmount()
     * @method \float[]                     getUfMaxAmountList()
     * @method \float[]                     fillUfMaxAmount()
     * @method \string[]                    getUfPaymentMethodIdsList()
     * @method \string[]                    fillUfPaymentMethodIds()
     * @method \int[]                       getUfPaymentPeriodList()
     * @method \int[]                       fillUfPaymentPeriod()
     * @method \float[]                     getUfFeeRateList()
     * @method \float[]                     fillUfFeeRate()
     * @method \string[]                    getUfConditionsList()
     * @method \string[]                    fillUfConditions()
     * @method \int[]                       getUfChatScriptIdList()
     * @method \int[]                       fillUfChatScriptId()
     * @method \string[]                    getUfStatusList()
     * @method \string[]                    fillUfStatus()
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
     * @method        void                                                                add(\Rebit\Exchange\Domain\Advertisement\Entity\Advertisement $object)
     * @method        bool                                                                has(\Rebit\Exchange\Domain\Advertisement\Entity\Advertisement $object)
     * @method        bool                                                                hasByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           getByPrimary($primary)
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement[]         getAll()
     * @method        bool                                                                remove(\Rebit\Exchange\Domain\Advertisement\Entity\Advertisement $object)
     * @method        void                                                                removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection                    fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                        save($ignoreEvents = false)
     * @method        void                                                                offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                                offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                                offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                                offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                                rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           current()                                                                                                                                                      Iterator
     * @method        mixed                                                               key()                                                                                                                                                          Iterator
     * @method        void                                                                next()                                                                                                                                                         Iterator
     * @method        bool                                                                valid()                                                                                                                                                        Iterator
     * @method        int                                                                 count()                                                                                                                                                        Countable
     * @method        \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection merge(?\Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection $collection)
     * @method        bool                                                                isEmpty()
     * @method        array                                                               collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_Advertisement_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Exchange\Domain\Advertisement\Entity\Table\AdvertisementTable
        public static $dataClass = '\Rebit\Exchange\Domain\Advertisement\Entity\Table\AdvertisementTable';
    }
}

namespace Rebit\Exchange\Domain\Advertisement\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_Advertisement_Query                                              query()
     * @method static EO_Advertisement_Result                                             getByPrimary($primary, array $parameters = [])
     * @method static EO_Advertisement_Result                                             getById($id)
     * @method static EO_Advertisement_Result                                             getList(array $parameters = [])
     * @method static EO_Advertisement_Entity                                             getEntity()
     * @method static \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           createObject($setDefaultValues = true)
     * @method static \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection createCollection()
     * @method static \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           wakeUpObject($row)
     * @method static \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection wakeUpCollection($rows)
     */
    class AdvertisementTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_Advertisement_Result                                             exec()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           fetchObject()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection fetchCollection()
     */
    class EO_Advertisement_Query extends Query {}
    /**
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           fetchObject()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection fetchCollection()
     */
    class EO_Advertisement_Result extends Result {}
    /**
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           createObject($setDefaultValues = true)
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection createCollection()
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\Advertisement           wakeUpObject($row)
     * @method \Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection wakeUpCollection($rows)
     */
    class EO_Advertisement_Entity extends Entity {}
}
