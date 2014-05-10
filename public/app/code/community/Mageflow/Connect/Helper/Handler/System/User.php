<?php
/**
 *
 * User.php
 *
 * @author  sven
 * @created 02/26/2014 14:37
 */

class Mageflow_Connect_Helper_Handler_System_User
    extends Mageflow_Connect_Helper_Handler_Abstract
{

    /**
     * update or create  from data array
     *
     * @param $filteredData
     *
     * @return array|null
     */
    public function handle($filteredData)
    {
        $itemFoundByIdentifier = false;
        $itemFoundByMfGuid = false;
        $foundItemsMatch = false;
        $itemModel = null;

        $itemModelByIdentifier = Mage::getModel('admin/user')
            ->load($filteredData['username'], 'username');
        $itemModelByMfGuid = Mage::getModel('admin/user')
            ->load($filteredData['mf_guid'], 'mf_guid');

        if ($itemModelByIdentifier->getUserId()) {
            $itemFoundByIdentifier = true;
        }
        if ($itemModelByMfGuid->getUserId()) {
            $itemFoundByMfGuid = true;
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid) {
            $idByIdent = $itemModelByIdentifier->getUserId();
            $idByGuid = $itemModelByMfGuid->getUserId();

            Mage::helper('mageflow_connect/log')->log(
                'by mf_guid ' . $idByGuid
            );
            Mage::helper('mageflow_connect/log')->log('by ident ' . $idByIdent);

            if ($idByGuid == $idByIdent) {
                $foundItemsMatch = true;
            }
        }

        if ($itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 01');
            $itemModel = $itemModelByIdentifier;
            $filteredData['user_id'] = $itemModel->getUserId();
        }
        if (!$itemFoundByIdentifier && $itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 10');
            $itemModel = $itemModelByMfGuid;
            $filteredData['user_id'] = $itemModel->getUserId();
        }
        if (!$itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 00');
            $itemModel = Mage::getModel('admin/user');
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && $foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-1');
            $itemModel = $itemModelByMfGuid;
            $filteredData['user_id'] = $itemModel->getUserId();
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && !$foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-0');
            $itemModel = $itemModelByMfGuid;
            $filteredData['user_id'] = $itemModel->getUserId();
        }

        $originalData = null;
        if (!is_null($itemModel)) {
            $originalData = $itemModel->getData();
        }

        Mage::helper('mageflow_connect/log')->log($originalData);

        foreach ($filteredData['roles'] as $key => $roleName) {
            $roleEntity = Mage::getModel('admin/role')
                ->load($roleName, 'role_name');
            Mage::helper('mageflow_connect/log')->log($roleEntity);
            if ($roleEntity->getRoleName() != '') {
                $filteredData['roles'][$key] = $roleEntity->getRoleId();
            } else {
                unset($filteredData['roles'][$key]);
            }
        }

        $model = $this->saveItem($itemModel, $filteredData);
        if ($model instanceof Mage_Admin_Model_User) {
            if (isset($filteredData['roles'])) {
                $model->setRoleIds($filteredData['roles'])
                    ->setRoleUserId($model->getUserId())
                    ->saveRelations();
            }
            //save user roles
            return array(
                'entity'        => $model,
                'original_data' => $originalData
            );
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save User. Data follows:\n"
            . print_r($filteredData, true)
        );
        return null;
    }

    /**
     * @param $content
     */
    public function packContent($content)
    {
        if (isset($content['password_confirmation'])) {
            unset($content['password_confirmation']);
        }
        foreach ($content['roles'] as $key => $roleId) {
            $roleEntity = Mage::getModel('admin/role')
                ->load($roleId, 'role_id');
            Mage::helper('mageflow_connect/log')->log($roleEntity);
            if ($roleEntity->getRoleName() != '') {
                $content['roles'][$key] = $roleEntity->getRoleName();
            } else {
                unset($content['roles'][$key]);
            }
        }
        return $content;
    }
}