<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\User;
use common\models\Category;
use common\models\Performance;
use common\models\Product;

use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class m150108_155243_init extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function up()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARSET=utf8 COLLATE=utf8_unicode_ci ENGINE=InnoDB';
        }

        		
                $this->execute('/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
                                /*!40101 SET NAMES utf8mb4 */;
                                /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
                                /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */;');
                
		// Dow CRM Tables
		$this->createTable(User::tableName(), [
            'UserId' => Schema::TYPE_INTEGER.' AUTO_INCREMENT NOT NULL',
			'Username' => Schema::TYPE_STRING . '(255) NOT NULL',
			'AuthKey' => Schema::TYPE_STRING . '(30) NOT NULL',
			'PasswordHash' => Schema::TYPE_STRING . '(255) NOT NULL',
			'PasswordResetToken' => Schema::TYPE_STRING . '(255)',
			'Email' => Schema::TYPE_STRING . ' NOT NULL',
			'CreatedAt' => Schema::TYPE_DATETIME . ' NOT NULL',
			'UpdatedAt' => Schema::TYPE_DATETIME . ' NOT NULL',
			'IsActive' => 'BIT (1) NOT NULL DEFAULT b\'1\' ',
                        'PRIMARY KEY (UserId)'
        ], $tableOptions);

		// AuthManager
		
        $this->createTable($authManager->ruleTable, [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createTable($authManager->itemTable, [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING . '(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . $authManager->ruleTable . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        $this->createTable($authManager->itemChildTable, [
            'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
            'child' => Schema::TYPE_STRING . '(64) NOT NULL',
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable($authManager->assignmentTable, [
            'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name, user_id)',
            ' FOREIGN KEY (item_name) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
	   'CONSTRAINT `FkAuthAssignment_UserId` FOREIGN KEY (user_id) REFERENCES ' . User::tableName() . ' (UserID) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        
        // Category
        $this->createTable(Category::tableName(), [
            'CategoryId' => Schema::TYPE_INTEGER . '(11) AUTO_INCREMENT NOT NULL',
            'Name' => Schema::TYPE_STRING . '(50) NOT NULL',
            'IsActive' => ' BIT(1) DEFAULT B\'1\'NOT NULL',
            'PRIMARY KEY (CategoryId)',            
        ], $tableOptions);
        
        // Performance
         $this->createTable(Performance::tableName(), [
            'PerformanceId' => Schema::TYPE_INTEGER . '(11) AUTO_INCREMENT NULL',
            'CategoryId' => Schema::TYPE_INTEGER . '(11) NULL',
            'Name' => Schema::TYPE_STRING . '(50)  NOT NULL',             
            'IsActive' => ' BIT(1) DEFAULT B\'1\' NOT NULL',
            'PRIMARY KEY (PerformanceId)',  
            'CONSTRAINT `FkPerformance_CategoryId` FOREIGN KEY (CategoryId) REFERENCES ' . Category::tableName(). ' (CategoryId) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions); 
         
        // Product
         $this->createTable(Product::tableName(), [
            'ProductId' => Schema::TYPE_INTEGER . '(11) AUTO_INCREMENT NOT NULL',
            'PerformanceId' => Schema::TYPE_INTEGER . '(11) NULL',
            'Name' => Schema::TYPE_STRING . '(50)  NOT NULL',             
            'IsActive' => ' BIT(1) DEFAULT B\'1\' NOT NULL',
            'PRIMARY KEY (ProductId)',  
            'CONSTRAINT `FkProduct_PerformanceId` FOREIGN KEY (PerformanceId) REFERENCES ' . Performance::tableName(). ' (PerformanceId) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
         
         
         $this->execute('/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, \'\') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;');
        
    }

    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
        
        $this->dropTable(Category::tableName());
        $this->dropTable(Performance::tableName());
        $this->dropTable(Product::tableName());                
    }
}
