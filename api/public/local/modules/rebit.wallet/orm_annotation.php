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
// ORMENTITYANNOTATION:RebitBalanceTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitBalance
     *
     * @see RebitBalanceTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitBalance                setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\int                       getUfUserId()
     * @method \EO_RebitBalance                setUfUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                            hasUfUserId()
     * @method bool                            isUfUserIdFilled()
     * @method bool                            isUfUserIdChanged()
     * @method null|\int                       remindActualUfUserId()
     * @method null|\int                       requireUfUserId()
     * @method \EO_RebitBalance                resetUfUserId()
     * @method \EO_RebitBalance                unsetUfUserId()
     * @method null|\int                       fillUfUserId()
     * @method null|\int                       getUfCurrencyId()
     * @method \EO_RebitBalance                setUfCurrencyId(null|\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyId)
     * @method bool                            hasUfCurrencyId()
     * @method bool                            isUfCurrencyIdFilled()
     * @method bool                            isUfCurrencyIdChanged()
     * @method null|\int                       remindActualUfCurrencyId()
     * @method null|\int                       requireUfCurrencyId()
     * @method \EO_RebitBalance                resetUfCurrencyId()
     * @method \EO_RebitBalance                unsetUfCurrencyId()
     * @method null|\int                       fillUfCurrencyId()
     * @method null|\float                     getUfAvailable()
     * @method \EO_RebitBalance                setUfAvailable(null|\Bitrix\Main\DB\SqlExpression|\float $ufAvailable)
     * @method bool                            hasUfAvailable()
     * @method bool                            isUfAvailableFilled()
     * @method bool                            isUfAvailableChanged()
     * @method null|\float                     remindActualUfAvailable()
     * @method null|\float                     requireUfAvailable()
     * @method \EO_RebitBalance                resetUfAvailable()
     * @method \EO_RebitBalance                unsetUfAvailable()
     * @method null|\float                     fillUfAvailable()
     * @method null|\float                     getUfLocked()
     * @method \EO_RebitBalance                setUfLocked(null|\Bitrix\Main\DB\SqlExpression|\float $ufLocked)
     * @method bool                            hasUfLocked()
     * @method bool                            isUfLockedFilled()
     * @method bool                            isUfLockedChanged()
     * @method null|\float                     remindActualUfLocked()
     * @method null|\float                     requireUfLocked()
     * @method \EO_RebitBalance                resetUfLocked()
     * @method \EO_RebitBalance                unsetUfLocked()
     * @method null|\float                     fillUfLocked()
     * @method null|\float                     getUfTotal()
     * @method \EO_RebitBalance                setUfTotal(null|\Bitrix\Main\DB\SqlExpression|\float $ufTotal)
     * @method bool                            hasUfTotal()
     * @method bool                            isUfTotalFilled()
     * @method bool                            isUfTotalChanged()
     * @method null|\float                     remindActualUfTotal()
     * @method null|\float                     requireUfTotal()
     * @method \EO_RebitBalance                resetUfTotal()
     * @method \EO_RebitBalance                unsetUfTotal()
     * @method null|\float                     fillUfTotal()
     * @method null|\Bitrix\Main\Type\DateTime getUfSyncedAt()
     * @method \EO_RebitBalance                setUfSyncedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufSyncedAt)
     * @method bool                            hasUfSyncedAt()
     * @method bool                            isUfSyncedAtFilled()
     * @method bool                            isUfSyncedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfSyncedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfSyncedAt()
     * @method \EO_RebitBalance                resetUfSyncedAt()
     * @method \EO_RebitBalance                unsetUfSyncedAt()
     * @method null|\Bitrix\Main\Type\DateTime fillUfSyncedAt()
     * @method null|\Bitrix\Main\Type\DateTime getUfUpdatedAt()
     * @method \EO_RebitBalance                setUfUpdatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                            hasUfUpdatedAt()
     * @method bool                            isUfUpdatedAtFilled()
     * @method bool                            isUfUpdatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfUpdatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfUpdatedAt()
     * @method \EO_RebitBalance                resetUfUpdatedAt()
     * @method \EO_RebitBalance                unsetUfUpdatedAt()
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
     * @method        \EO_RebitBalance                                                                                set($fieldName, $value)
     * @method        \EO_RebitBalance                                                                                reset($fieldName)
     * @method        \EO_RebitBalance                                                                                unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitBalance                                                                                wakeUp($data)
     */
    class EO_RebitBalance
    {
        // @var \RebitBalanceTable
        public static $dataClass = '\RebitBalanceTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitBalance_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\int[]                       getUfUserIdList()
     * @method null|\int[]                       fillUfUserId()
     * @method null|\int[]                       getUfCurrencyIdList()
     * @method null|\int[]                       fillUfCurrencyId()
     * @method null|\float[]                     getUfAvailableList()
     * @method null|\float[]                     fillUfAvailable()
     * @method null|\float[]                     getUfLockedList()
     * @method null|\float[]                     fillUfLocked()
     * @method null|\float[]                     getUfTotalList()
     * @method null|\float[]                     fillUfTotal()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfSyncedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfSyncedAt()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfUpdatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitBalance $object)
     * @method        bool                                             has(\EO_RebitBalance $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitBalance                                 getByPrimary($primary)
     * @method        \EO_RebitBalance[]                               getAll()
     * @method        bool                                             remove(\EO_RebitBalance $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitBalance_Collection                      wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitBalance                                 current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitBalance_Collection                      merge(?\EO_RebitBalance_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitBalance_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitBalanceTable
        public static $dataClass = '\RebitBalanceTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitBalance_Query       query()
     * @method static EO_RebitBalance_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitBalance_Result      getById($id)
     * @method static EO_RebitBalance_Result      getList(array $parameters = [])
     * @method static EO_RebitBalance_Entity      getEntity()
     * @method static \EO_RebitBalance            createObject($setDefaultValues = true)
     * @method static \EO_RebitBalance_Collection createCollection()
     * @method static \EO_RebitBalance            wakeUpObject($row)
     * @method static \EO_RebitBalance_Collection wakeUpCollection($rows)
     */
    class RebitBalanceTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitBalance_Result      exec()
     * @method \EO_RebitBalance            fetchObject()
     * @method \EO_RebitBalance_Collection fetchCollection()
     */
    class EO_RebitBalance_Query extends Query {}
    /**
     * @method \EO_RebitBalance            fetchObject()
     * @method \EO_RebitBalance_Collection fetchCollection()
     */
    class EO_RebitBalance_Result extends Result {}
    /**
     * @method \EO_RebitBalance            createObject($setDefaultValues = true)
     * @method \EO_RebitBalance_Collection createCollection()
     * @method \EO_RebitBalance            wakeUpObject($row)
     * @method \EO_RebitBalance_Collection wakeUpCollection($rows)
     */
    class EO_RebitBalance_Entity extends Entity {}
}
// ORMENTITYANNOTATION:RebitTransactionTable

namespace {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * EO_RebitTransaction
     *
     * @see RebitTransactionTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                            getId()
     * @method \EO_RebitTransaction            setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                            hasId()
     * @method bool                            isIdFilled()
     * @method bool                            isIdChanged()
     * @method null|\int                       getUfUserId()
     * @method \EO_RebitTransaction            setUfUserId(null|\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                            hasUfUserId()
     * @method bool                            isUfUserIdFilled()
     * @method bool                            isUfUserIdChanged()
     * @method null|\int                       remindActualUfUserId()
     * @method null|\int                       requireUfUserId()
     * @method \EO_RebitTransaction            resetUfUserId()
     * @method \EO_RebitTransaction            unsetUfUserId()
     * @method null|\int                       fillUfUserId()
     * @method null|\int                       getUfCurrencyId()
     * @method \EO_RebitTransaction            setUfCurrencyId(null|\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyId)
     * @method bool                            hasUfCurrencyId()
     * @method bool                            isUfCurrencyIdFilled()
     * @method bool                            isUfCurrencyIdChanged()
     * @method null|\int                       remindActualUfCurrencyId()
     * @method null|\int                       requireUfCurrencyId()
     * @method \EO_RebitTransaction            resetUfCurrencyId()
     * @method \EO_RebitTransaction            unsetUfCurrencyId()
     * @method null|\int                       fillUfCurrencyId()
     * @method null|\string                    getUfType()
     * @method \EO_RebitTransaction            setUfType(null|\Bitrix\Main\DB\SqlExpression|\string $ufType)
     * @method bool                            hasUfType()
     * @method bool                            isUfTypeFilled()
     * @method bool                            isUfTypeChanged()
     * @method null|\string                    remindActualUfType()
     * @method null|\string                    requireUfType()
     * @method \EO_RebitTransaction            resetUfType()
     * @method \EO_RebitTransaction            unsetUfType()
     * @method null|\string                    fillUfType()
     * @method null|\float                     getUfAmount()
     * @method \EO_RebitTransaction            setUfAmount(null|\Bitrix\Main\DB\SqlExpression|\float $ufAmount)
     * @method bool                            hasUfAmount()
     * @method bool                            isUfAmountFilled()
     * @method bool                            isUfAmountChanged()
     * @method null|\float                     remindActualUfAmount()
     * @method null|\float                     requireUfAmount()
     * @method \EO_RebitTransaction            resetUfAmount()
     * @method \EO_RebitTransaction            unsetUfAmount()
     * @method null|\float                     fillUfAmount()
     * @method null|\float                     getUfBalanceAfter()
     * @method \EO_RebitTransaction            setUfBalanceAfter(null|\Bitrix\Main\DB\SqlExpression|\float $ufBalanceAfter)
     * @method bool                            hasUfBalanceAfter()
     * @method bool                            isUfBalanceAfterFilled()
     * @method bool                            isUfBalanceAfterChanged()
     * @method null|\float                     remindActualUfBalanceAfter()
     * @method null|\float                     requireUfBalanceAfter()
     * @method \EO_RebitTransaction            resetUfBalanceAfter()
     * @method \EO_RebitTransaction            unsetUfBalanceAfter()
     * @method null|\float                     fillUfBalanceAfter()
     * @method null|\int                       getUfTradeId()
     * @method \EO_RebitTransaction            setUfTradeId(null|\Bitrix\Main\DB\SqlExpression|\int $ufTradeId)
     * @method bool                            hasUfTradeId()
     * @method bool                            isUfTradeIdFilled()
     * @method bool                            isUfTradeIdChanged()
     * @method null|\int                       remindActualUfTradeId()
     * @method null|\int                       requireUfTradeId()
     * @method \EO_RebitTransaction            resetUfTradeId()
     * @method \EO_RebitTransaction            unsetUfTradeId()
     * @method null|\int                       fillUfTradeId()
     * @method null|\string                    getUfDescription()
     * @method \EO_RebitTransaction            setUfDescription(null|\Bitrix\Main\DB\SqlExpression|\string $ufDescription)
     * @method bool                            hasUfDescription()
     * @method bool                            isUfDescriptionFilled()
     * @method bool                            isUfDescriptionChanged()
     * @method null|\string                    remindActualUfDescription()
     * @method null|\string                    requireUfDescription()
     * @method \EO_RebitTransaction            resetUfDescription()
     * @method \EO_RebitTransaction            unsetUfDescription()
     * @method null|\string                    fillUfDescription()
     * @method null|\string                    getUfBybitTxId()
     * @method \EO_RebitTransaction            setUfBybitTxId(null|\Bitrix\Main\DB\SqlExpression|\string $ufBybitTxId)
     * @method bool                            hasUfBybitTxId()
     * @method bool                            isUfBybitTxIdFilled()
     * @method bool                            isUfBybitTxIdChanged()
     * @method null|\string                    remindActualUfBybitTxId()
     * @method null|\string                    requireUfBybitTxId()
     * @method \EO_RebitTransaction            resetUfBybitTxId()
     * @method \EO_RebitTransaction            unsetUfBybitTxId()
     * @method null|\string                    fillUfBybitTxId()
     * @method null|\Bitrix\Main\Type\DateTime getUfCreatedAt()
     * @method \EO_RebitTransaction            setUfCreatedAt(null|\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                            hasUfCreatedAt()
     * @method bool                            isUfCreatedAtFilled()
     * @method bool                            isUfCreatedAtChanged()
     * @method null|\Bitrix\Main\Type\DateTime remindActualUfCreatedAt()
     * @method null|\Bitrix\Main\Type\DateTime requireUfCreatedAt()
     * @method \EO_RebitTransaction            resetUfCreatedAt()
     * @method \EO_RebitTransaction            unsetUfCreatedAt()
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
     * @method        \EO_RebitTransaction                                                                            set($fieldName, $value)
     * @method        \EO_RebitTransaction                                                                            reset($fieldName)
     * @method        \EO_RebitTransaction                                                                            unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \EO_RebitTransaction                                                                            wakeUp($data)
     */
    class EO_RebitTransaction
    {
        // @var \RebitTransactionTable
        public static $dataClass = '\RebitTransactionTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace {
    use Bitrix\Main\ORM\Entity;

    /**
     * EO_RebitTransaction_Collection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                            getIdList()
     * @method null|\int[]                       getUfUserIdList()
     * @method null|\int[]                       fillUfUserId()
     * @method null|\int[]                       getUfCurrencyIdList()
     * @method null|\int[]                       fillUfCurrencyId()
     * @method null|\string[]                    getUfTypeList()
     * @method null|\string[]                    fillUfType()
     * @method null|\float[]                     getUfAmountList()
     * @method null|\float[]                     fillUfAmount()
     * @method null|\float[]                     getUfBalanceAfterList()
     * @method null|\float[]                     fillUfBalanceAfter()
     * @method null|\int[]                       getUfTradeIdList()
     * @method null|\int[]                       fillUfTradeId()
     * @method null|\string[]                    getUfDescriptionList()
     * @method null|\string[]                    fillUfDescription()
     * @method null|\string[]                    getUfBybitTxIdList()
     * @method null|\string[]                    fillUfBybitTxId()
     * @method null|\Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method null|\Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                             add(\EO_RebitTransaction $object)
     * @method        bool                                             has(\EO_RebitTransaction $object)
     * @method        bool                                             hasByPrimary($primary)
     * @method        \EO_RebitTransaction                             getByPrimary($primary)
     * @method        \EO_RebitTransaction[]                           getAll()
     * @method        bool                                             remove(\EO_RebitTransaction $object)
     * @method        void                                             removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \EO_RebitTransaction_Collection                  wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                     save($ignoreEvents = false)
     * @method        void                                             offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                             offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                             offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                             offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                             rewind()                                                                                                                                                       Iterator
     * @method        \EO_RebitTransaction                             current()                                                                                                                                                      Iterator
     * @method        mixed                                            key()                                                                                                                                                          Iterator
     * @method        void                                             next()                                                                                                                                                         Iterator
     * @method        bool                                             valid()                                                                                                                                                        Iterator
     * @method        int                                              count()                                                                                                                                                        Countable
     * @method        \EO_RebitTransaction_Collection                  merge(?\EO_RebitTransaction_Collection $collection)
     * @method        bool                                             isEmpty()
     * @method        array                                            collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_RebitTransaction_Collection implements ArrayAccess, Iterator, Countable
    {
        // @var \RebitTransactionTable
        public static $dataClass = '\RebitTransactionTable';
    }
}

namespace {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_RebitTransaction_Query       query()
     * @method static EO_RebitTransaction_Result      getByPrimary($primary, array $parameters = [])
     * @method static EO_RebitTransaction_Result      getById($id)
     * @method static EO_RebitTransaction_Result      getList(array $parameters = [])
     * @method static EO_RebitTransaction_Entity      getEntity()
     * @method static \EO_RebitTransaction            createObject($setDefaultValues = true)
     * @method static \EO_RebitTransaction_Collection createCollection()
     * @method static \EO_RebitTransaction            wakeUpObject($row)
     * @method static \EO_RebitTransaction_Collection wakeUpCollection($rows)
     */
    class RebitTransactionTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_RebitTransaction_Result      exec()
     * @method \EO_RebitTransaction            fetchObject()
     * @method \EO_RebitTransaction_Collection fetchCollection()
     */
    class EO_RebitTransaction_Query extends Query {}
    /**
     * @method \EO_RebitTransaction            fetchObject()
     * @method \EO_RebitTransaction_Collection fetchCollection()
     */
    class EO_RebitTransaction_Result extends Result {}
    /**
     * @method \EO_RebitTransaction            createObject($setDefaultValues = true)
     * @method \EO_RebitTransaction_Collection createCollection()
     * @method \EO_RebitTransaction            wakeUpObject($row)
     * @method \EO_RebitTransaction_Collection wakeUpCollection($rows)
     */
    class EO_RebitTransaction_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Wallet\Domain\Transaction\Entity\Table\TransactionTable

namespace Rebit\Wallet\Domain\Transaction\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * Transaction
     *
     * @see TransactionTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                                getId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                                hasId()
     * @method bool                                                isIdFilled()
     * @method bool                                                isIdChanged()
     * @method \int                                                getUfUserId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfUserId(\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                                                hasUfUserId()
     * @method bool                                                isUfUserIdFilled()
     * @method bool                                                isUfUserIdChanged()
     * @method \int                                                remindActualUfUserId()
     * @method \int                                                requireUfUserId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfUserId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfUserId()
     * @method \int                                                fillUfUserId()
     * @method \int                                                getUfCurrencyId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfCurrencyId(\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyId)
     * @method bool                                                hasUfCurrencyId()
     * @method bool                                                isUfCurrencyIdFilled()
     * @method bool                                                isUfCurrencyIdChanged()
     * @method \int                                                remindActualUfCurrencyId()
     * @method \int                                                requireUfCurrencyId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfCurrencyId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfCurrencyId()
     * @method \int                                                fillUfCurrencyId()
     * @method \string                                             getUfType()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfType(\Bitrix\Main\DB\SqlExpression|\string $ufType)
     * @method bool                                                hasUfType()
     * @method bool                                                isUfTypeFilled()
     * @method bool                                                isUfTypeChanged()
     * @method \string                                             remindActualUfType()
     * @method \string                                             requireUfType()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfType()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfType()
     * @method \string                                             fillUfType()
     * @method \float                                              getUfAmount()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfAmount(\Bitrix\Main\DB\SqlExpression|\float $ufAmount)
     * @method bool                                                hasUfAmount()
     * @method bool                                                isUfAmountFilled()
     * @method bool                                                isUfAmountChanged()
     * @method \float                                              remindActualUfAmount()
     * @method \float                                              requireUfAmount()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfAmount()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfAmount()
     * @method \float                                              fillUfAmount()
     * @method \float                                              getUfBalanceAfter()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfBalanceAfter(\Bitrix\Main\DB\SqlExpression|\float $ufBalanceAfter)
     * @method bool                                                hasUfBalanceAfter()
     * @method bool                                                isUfBalanceAfterFilled()
     * @method bool                                                isUfBalanceAfterChanged()
     * @method \float                                              remindActualUfBalanceAfter()
     * @method \float                                              requireUfBalanceAfter()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfBalanceAfter()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfBalanceAfter()
     * @method \float                                              fillUfBalanceAfter()
     * @method \int                                                getUfTradeId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfTradeId(\Bitrix\Main\DB\SqlExpression|\int $ufTradeId)
     * @method bool                                                hasUfTradeId()
     * @method bool                                                isUfTradeIdFilled()
     * @method bool                                                isUfTradeIdChanged()
     * @method \int                                                remindActualUfTradeId()
     * @method \int                                                requireUfTradeId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfTradeId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfTradeId()
     * @method \int                                                fillUfTradeId()
     * @method \string                                             getUfDescription()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfDescription(\Bitrix\Main\DB\SqlExpression|\string $ufDescription)
     * @method bool                                                hasUfDescription()
     * @method bool                                                isUfDescriptionFilled()
     * @method bool                                                isUfDescriptionChanged()
     * @method \string                                             remindActualUfDescription()
     * @method \string                                             requireUfDescription()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfDescription()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfDescription()
     * @method \string                                             fillUfDescription()
     * @method \string                                             getUfBybitTxId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfBybitTxId(\Bitrix\Main\DB\SqlExpression|\string $ufBybitTxId)
     * @method bool                                                hasUfBybitTxId()
     * @method bool                                                isUfBybitTxIdFilled()
     * @method bool                                                isUfBybitTxIdChanged()
     * @method \string                                             remindActualUfBybitTxId()
     * @method \string                                             requireUfBybitTxId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfBybitTxId()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfBybitTxId()
     * @method \string                                             fillUfBybitTxId()
     * @method \Bitrix\Main\Type\DateTime                          getUfCreatedAt()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction setUfCreatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufCreatedAt)
     * @method bool                                                hasUfCreatedAt()
     * @method bool                                                isUfCreatedAtFilled()
     * @method bool                                                isUfCreatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                          remindActualUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                          requireUfCreatedAt()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction resetUfCreatedAt()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction unsetUfCreatedAt()
     * @method \Bitrix\Main\Type\DateTime                          fillUfCreatedAt()
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
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\Transaction                                             set($fieldName, $value)
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\Transaction                                             reset($fieldName)
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\Transaction                                             unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Wallet\Domain\Transaction\Entity\Transaction                                             wakeUp($data)
     */
    class EO_Transaction
    {
        // @var \Rebit\Wallet\Domain\Transaction\Entity\Table\TransactionTable
        public static $dataClass = '\Rebit\Wallet\Domain\Transaction\Entity\Table\TransactionTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Wallet\Domain\Transaction\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * TransactionCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \int[]                       getUfUserIdList()
     * @method \int[]                       fillUfUserId()
     * @method \int[]                       getUfCurrencyIdList()
     * @method \int[]                       fillUfCurrencyId()
     * @method \string[]                    getUfTypeList()
     * @method \string[]                    fillUfType()
     * @method \float[]                     getUfAmountList()
     * @method \float[]                     fillUfAmount()
     * @method \float[]                     getUfBalanceAfterList()
     * @method \float[]                     fillUfBalanceAfter()
     * @method \int[]                       getUfTradeIdList()
     * @method \int[]                       fillUfTradeId()
     * @method \string[]                    getUfDescriptionList()
     * @method \string[]                    fillUfDescription()
     * @method \string[]                    getUfBybitTxIdList()
     * @method \string[]                    fillUfBybitTxId()
     * @method \Bitrix\Main\Type\DateTime[] getUfCreatedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfCreatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                          add(\Rebit\Wallet\Domain\Transaction\Entity\Transaction $object)
     * @method        bool                                                          has(\Rebit\Wallet\Domain\Transaction\Entity\Transaction $object)
     * @method        bool                                                          hasByPrimary($primary)
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\Transaction           getByPrimary($primary)
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\Transaction[]         getAll()
     * @method        bool                                                          remove(\Rebit\Wallet\Domain\Transaction\Entity\Transaction $object)
     * @method        void                                                          removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection              fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                                  save($ignoreEvents = false)
     * @method        void                                                          offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                          offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                          offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                          offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                          rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\Transaction           current()                                                                                                                                                      Iterator
     * @method        mixed                                                         key()                                                                                                                                                          Iterator
     * @method        void                                                          next()                                                                                                                                                         Iterator
     * @method        bool                                                          valid()                                                                                                                                                        Iterator
     * @method        int                                                           count()                                                                                                                                                        Countable
     * @method        \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection merge(?\Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection $collection)
     * @method        bool                                                          isEmpty()
     * @method        array                                                         collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_Transaction_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Wallet\Domain\Transaction\Entity\Table\TransactionTable
        public static $dataClass = '\Rebit\Wallet\Domain\Transaction\Entity\Table\TransactionTable';
    }
}

namespace Rebit\Wallet\Domain\Transaction\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_Transaction_Query                                          query()
     * @method static EO_Transaction_Result                                         getByPrimary($primary, array $parameters = [])
     * @method static EO_Transaction_Result                                         getById($id)
     * @method static EO_Transaction_Result                                         getList(array $parameters = [])
     * @method static EO_Transaction_Entity                                         getEntity()
     * @method static \Rebit\Wallet\Domain\Transaction\Entity\Transaction           createObject($setDefaultValues = true)
     * @method static \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection createCollection()
     * @method static \Rebit\Wallet\Domain\Transaction\Entity\Transaction           wakeUpObject($row)
     * @method static \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection wakeUpCollection($rows)
     */
    class TransactionTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_Transaction_Result                                         exec()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction           fetchObject()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection fetchCollection()
     */
    class EO_Transaction_Query extends Query {}
    /**
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction           fetchObject()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection fetchCollection()
     */
    class EO_Transaction_Result extends Result {}
    /**
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction           createObject($setDefaultValues = true)
     * @method \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection createCollection()
     * @method \Rebit\Wallet\Domain\Transaction\Entity\Transaction           wakeUpObject($row)
     * @method \Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection wakeUpCollection($rows)
     */
    class EO_Transaction_Entity extends Entity {}
}
// ORMENTITYANNOTATION:Rebit\Wallet\Domain\Balance\Entity\Table\BalanceTable

namespace Rebit\Wallet\Domain\Balance\Entity\Table {
    use Bitrix\Main\Authentication\Context;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\Type\Dictionary;

    /**
     * Balance
     *
     * @see BalanceTable
     *
     * Custom methods:
     * ---------------
     *
     * @method \int                                        getId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setId(\Bitrix\Main\DB\SqlExpression|\int $id)
     * @method bool                                        hasId()
     * @method bool                                        isIdFilled()
     * @method bool                                        isIdChanged()
     * @method \int                                        getUfUserId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfUserId(\Bitrix\Main\DB\SqlExpression|\int $ufUserId)
     * @method bool                                        hasUfUserId()
     * @method bool                                        isUfUserIdFilled()
     * @method bool                                        isUfUserIdChanged()
     * @method \int                                        remindActualUfUserId()
     * @method \int                                        requireUfUserId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfUserId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfUserId()
     * @method \int                                        fillUfUserId()
     * @method \int                                        getUfCurrencyId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfCurrencyId(\Bitrix\Main\DB\SqlExpression|\int $ufCurrencyId)
     * @method bool                                        hasUfCurrencyId()
     * @method bool                                        isUfCurrencyIdFilled()
     * @method bool                                        isUfCurrencyIdChanged()
     * @method \int                                        remindActualUfCurrencyId()
     * @method \int                                        requireUfCurrencyId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfCurrencyId()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfCurrencyId()
     * @method \int                                        fillUfCurrencyId()
     * @method \float                                      getUfAvailable()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfAvailable(\Bitrix\Main\DB\SqlExpression|\float $ufAvailable)
     * @method bool                                        hasUfAvailable()
     * @method bool                                        isUfAvailableFilled()
     * @method bool                                        isUfAvailableChanged()
     * @method \float                                      remindActualUfAvailable()
     * @method \float                                      requireUfAvailable()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfAvailable()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfAvailable()
     * @method \float                                      fillUfAvailable()
     * @method \float                                      getUfLocked()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfLocked(\Bitrix\Main\DB\SqlExpression|\float $ufLocked)
     * @method bool                                        hasUfLocked()
     * @method bool                                        isUfLockedFilled()
     * @method bool                                        isUfLockedChanged()
     * @method \float                                      remindActualUfLocked()
     * @method \float                                      requireUfLocked()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfLocked()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfLocked()
     * @method \float                                      fillUfLocked()
     * @method \float                                      getUfTotal()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfTotal(\Bitrix\Main\DB\SqlExpression|\float $ufTotal)
     * @method bool                                        hasUfTotal()
     * @method bool                                        isUfTotalFilled()
     * @method bool                                        isUfTotalChanged()
     * @method \float                                      remindActualUfTotal()
     * @method \float                                      requireUfTotal()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfTotal()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfTotal()
     * @method \float                                      fillUfTotal()
     * @method \Bitrix\Main\Type\DateTime                  getUfSyncedAt()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfSyncedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufSyncedAt)
     * @method bool                                        hasUfSyncedAt()
     * @method bool                                        isUfSyncedAtFilled()
     * @method bool                                        isUfSyncedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                  remindActualUfSyncedAt()
     * @method \Bitrix\Main\Type\DateTime                  requireUfSyncedAt()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfSyncedAt()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfSyncedAt()
     * @method \Bitrix\Main\Type\DateTime                  fillUfSyncedAt()
     * @method \Bitrix\Main\Type\DateTime                  getUfUpdatedAt()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance setUfUpdatedAt(\Bitrix\Main\DB\SqlExpression|\Bitrix\Main\Type\DateTime $ufUpdatedAt)
     * @method bool                                        hasUfUpdatedAt()
     * @method bool                                        isUfUpdatedAtFilled()
     * @method bool                                        isUfUpdatedAtChanged()
     * @method \Bitrix\Main\Type\DateTime                  remindActualUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                  requireUfUpdatedAt()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance resetUfUpdatedAt()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance unsetUfUpdatedAt()
     * @method \Bitrix\Main\Type\DateTime                  fillUfUpdatedAt()
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
     * @method        \Rebit\Wallet\Domain\Balance\Entity\Balance                                                     set($fieldName, $value)
     * @method        \Rebit\Wallet\Domain\Balance\Entity\Balance                                                     reset($fieldName)
     * @method        \Rebit\Wallet\Domain\Balance\Entity\Balance                                                     unset($fieldName)
     * @method        void                                                                                            addTo($fieldName, $value)
     * @method        void                                                                                            removeFrom($fieldName, $value)
     * @method        void                                                                                            removeAll($fieldName)
     * @method        \Bitrix\Main\ORM\Data\Result                                                                    delete()
     * @method        mixed                                                                                           fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                    flag or array of field names
     * @method        mixed[]                                                                                         collectValues($valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)
     * @method        \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult save()
     * @method static \Rebit\Wallet\Domain\Balance\Entity\Balance                                                     wakeUp($data)
     */
    class EO_Balance
    {
        // @var \Rebit\Wallet\Domain\Balance\Entity\Table\BalanceTable
        public static $dataClass = '\Rebit\Wallet\Domain\Balance\Entity\Table\BalanceTable';

        /**
         * @param array|bool $setDefaultValues
         */
        public function __construct($setDefaultValues = true) {}
    }
}

namespace Rebit\Wallet\Domain\Balance\Entity\Table {
    use Bitrix\Main\ORM\Entity;

    /**
     * BalanceCollection
     *
     * Custom methods:
     * ---------------
     *
     * @method \int[]                       getIdList()
     * @method \int[]                       getUfUserIdList()
     * @method \int[]                       fillUfUserId()
     * @method \int[]                       getUfCurrencyIdList()
     * @method \int[]                       fillUfCurrencyId()
     * @method \float[]                     getUfAvailableList()
     * @method \float[]                     fillUfAvailable()
     * @method \float[]                     getUfLockedList()
     * @method \float[]                     fillUfLocked()
     * @method \float[]                     getUfTotalList()
     * @method \float[]                     fillUfTotal()
     * @method \Bitrix\Main\Type\DateTime[] getUfSyncedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfSyncedAt()
     * @method \Bitrix\Main\Type\DateTime[] getUfUpdatedAtList()
     * @method \Bitrix\Main\Type\DateTime[] fillUfUpdatedAt()
     *
     * Common methods:
     * ---------------
     *
     * @property Entity $entity
     *
     * @method        void                                                  add(\Rebit\Wallet\Domain\Balance\Entity\Balance $object)
     * @method        bool                                                  has(\Rebit\Wallet\Domain\Balance\Entity\Balance $object)
     * @method        bool                                                  hasByPrimary($primary)
     * @method        \Rebit\Wallet\Domain\Balance\Entity\Balance           getByPrimary($primary)
     * @method        \Rebit\Wallet\Domain\Balance\Entity\Balance[]         getAll()
     * @method        bool                                                  remove(\Rebit\Wallet\Domain\Balance\Entity\Balance $object)
     * @method        void                                                  removeByPrimary($primary)
     * @method        null|array|\Bitrix\Main\ORM\Objectify\Collection      fill($fields = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL)                                                                                                     flag or array of field names
     * @method static \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection wakeUp($data)
     * @method        \Bitrix\Main\ORM\Data\Result                          save($ignoreEvents = false)
     * @method        void                                                  offsetSet()                                                                                                                                                    ArrayAccess
     * @method        void                                                  offsetExists()                                                                                                                                                 ArrayAccess
     * @method        void                                                  offsetUnset()                                                                                                                                                  ArrayAccess
     * @method        void                                                  offsetGet()                                                                                                                                                    ArrayAccess
     * @method        void                                                  rewind()                                                                                                                                                       Iterator
     * @method        \Rebit\Wallet\Domain\Balance\Entity\Balance           current()                                                                                                                                                      Iterator
     * @method        mixed                                                 key()                                                                                                                                                          Iterator
     * @method        void                                                  next()                                                                                                                                                         Iterator
     * @method        bool                                                  valid()                                                                                                                                                        Iterator
     * @method        int                                                   count()                                                                                                                                                        Countable
     * @method        \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection merge(?\Rebit\Wallet\Domain\Balance\Entity\BalanceCollection $collection)
     * @method        bool                                                  isEmpty()
     * @method        array                                                 collectValues(int $valuesType = \Bitrix\Main\ORM\Objectify\Values::ALL, int $fieldsMask = \Bitrix\Main\ORM\Fields\FieldTypeMask::ALL, bool $recursive = false)
     */
    class EO_Balance_Collection implements \ArrayAccess, \Iterator, \Countable
    {
        // @var \Rebit\Wallet\Domain\Balance\Entity\Table\BalanceTable
        public static $dataClass = '\Rebit\Wallet\Domain\Balance\Entity\Table\BalanceTable';
    }
}

namespace Rebit\Wallet\Domain\Balance\Entity\Table {
    use Bitrix\Main\ORM\Data\DataManager;
    use Bitrix\Main\ORM\Entity;
    use Bitrix\Main\ORM\Query\Query;
    use Bitrix\Main\ORM\Query\Result;

    /**
     * @method static EO_Balance_Query                                      query()
     * @method static EO_Balance_Result                                     getByPrimary($primary, array $parameters = [])
     * @method static EO_Balance_Result                                     getById($id)
     * @method static EO_Balance_Result                                     getList(array $parameters = [])
     * @method static EO_Balance_Entity                                     getEntity()
     * @method static \Rebit\Wallet\Domain\Balance\Entity\Balance           createObject($setDefaultValues = true)
     * @method static \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection createCollection()
     * @method static \Rebit\Wallet\Domain\Balance\Entity\Balance           wakeUpObject($row)
     * @method static \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection wakeUpCollection($rows)
     */
    class BalanceTable extends DataManager {}
    /**
     * Common methods:
     * ---------------
     *
     * @method EO_Balance_Result                                     exec()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance           fetchObject()
     * @method \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection fetchCollection()
     */
    class EO_Balance_Query extends Query {}
    /**
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance           fetchObject()
     * @method \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection fetchCollection()
     */
    class EO_Balance_Result extends Result {}
    /**
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance           createObject($setDefaultValues = true)
     * @method \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection createCollection()
     * @method \Rebit\Wallet\Domain\Balance\Entity\Balance           wakeUpObject($row)
     * @method \Rebit\Wallet\Domain\Balance\Entity\BalanceCollection wakeUpCollection($rows)
     */
    class EO_Balance_Entity extends Entity {}
}
