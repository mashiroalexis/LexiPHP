<?php
/**
 * Copyright © Ramon Alexis Celis All rights reserved.
 * See license file for more info.
 */

Class Account_Model_Account extends Database_Model_Base {

	const STATUS_ACTIVE = "active";
	const STATUS_INACTIVE = "inactive";
	const STATUS_DISABLED = "disabled";

	/**
	 *	Table Name for this model
	 */
	protected $table = "account";

	/**
	 *	Search account by name
	 *	@param string $name
	 *	@return array $result
	 */
	public function queryAccountByName( $name ) {
		return $this->where("fname", $name)->orWhere("lname", $name)->get();
	}

	/**
	 *	Check if account is status
	 *	@param string $status
	 *	@return bool
	 */	
	public function isStatus(  ) {

	}

	/**
	 *	check if acount is being evaluated
	 */
	public function hasEvaluation( $id ) {
		$evaluationDb = Core::getModel("evaluation/evaluation");

		$evaluation = $evaluationDb->get();

		foreach( $evaluation as $eval ) {
			if( $eval->account_id  == $id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 *	check account type
	 */
	public function isAccountType( $account ) {
		$auth = Core::getSingleton("system/session")->get("auth");
		$accountTypeDb = Core::getModel("account/accounttype");
		$accountType = $accountTypeDb->where("id", $auth->account_type_id)->first();
		if( $account == $accountType->type or $account == $accountType->id) {
			return true;
		}
		return false;
	}

	/**
	 *	check if current login account is admin
	 *	@param int $id
	 *	@return bool $result
	 */
	public function isAdmin( $id = false ) {
		$session = Core::getSingleton("system/session");
		$auth = $session->get("auth");
		$accountDb = Core::getModel("account/account");
		$accountTypeDb = Core::getModel("account/accounttype");

		if( $id ) {
			$auth = $accountDb->where("id", $id)->first();
		}

		if( $auth->account_type_id == 1 ) {
			return true;
		}
		return false;
	}

	/**
	 *	Check if current user is Dean
	 *	@param int $id
	 *	@return bool $result
	 */
	public function isDean( $id = false ) {
		$user = Core::getSingleton("system/session")->get("auth");
		$accountDb = Core::getModel("account/account");
		$accountTypeDb = Core::getModel("account/accounttype");

		if( $id ) {
			$user = $accountDb->where("id", $id)->first();
		}

		if( $user->account_type_id == 2 ) {
			return true;
		}
		return false;
	}

	/**
	 *	Check if current user is Teacher
	 *	@param int $id
	 *	@return bool $result
	 */
	public function isTeacher( $id = false ) {
		$user = Core::getSingleton("system/session")->get("auth");
		$accountDb = Core::getModel("account/account");
		$accountTypeDb = Core::getModel("account/accounttype");

		if( $id ) {
			$user = $accountDb->where("id", $id)->first();
		}

		if( $user->account_type_id == 3 ) {
			return true;
		}
		return false;
	}	
	/**
	 *	Get Account Department
	 *	@param int $id
	 *	@return obj $deparment
	 */
	public function getDepartment( $id ) {
		$accountDataDb 			= Core::getModel("account/accountdata");
		$accountDepartmentDb 	= Core::getModel("account/department");

		return $accountDepartmentDb->where("id", $accountDataDb->where("account_id", $id)->first()->college_dept_id)->first();
		// $accountdata 			= $accountDataDb->where("account_id", $id)->first();
		// $accountDepartment 		= $accountDepartmentDb->where("id", $accountdata->college_dept_id)->first();
		// return $accountDepartment;
	}

	/**
	 *	department compare
	 */
	public function sameDepartment( $id, $id2 = false ) {
		$session 	= Core::getSingleton("system/session");
		$department = Core::getModel("account/department");
		$accountDb 	= Core::getModel("account/account");

		if(! $id2 ) {
			$auth = $session->get("auth");
			$id2 = $auth->id;
		}
		$dep1 = $accountDb->getDepartment($id);
		$dep2 = $accountDb->getDepartment($id2);
		if( $dep1->id == $dep2->id ) {
			return true;
		}
		return false;
	}
 
	/**
	 *	Get Account Subject
	 *	@var int $id
	 *	@return obj $subject
	 */
	public function getSubject( $id ) {
		$accountDataDb = Core::getModel("account/accountdata");
		$subjectDb = Core::getModel("admin/subject");

		$accountData = $accountDataDb->where( "account_id", $id )->first();
		return $subjectDb->where("id", $accountData->subject_id)->first();
	}

	/**
	 *	Get Year
	 */
	// public function getYear( $id ) {
	// 	$accountDataDb = Core::getModel("account/accountdata");
	// 	$accountData = $accountDataDb->where( "account_id", $id )->first();
	// 	return $accountData->
	// }

	/**
	 *	Get Account Data
	 *	@var int $id
	 *	@return obj $account
	 */
	public function getAccountData( $id ) {
		$accountDataDb = Core::getModel("account/accountdata");
		$rs = $accountDataDb->where("account_id", $id)->first();
		if( $rs ) {
			return $rs;
		}
		return false;
	}

	/**
	 *	Get Account Type
	 *	@var int $id
	 *	@return obj $type
	 */
	public function getAccountType( $id ) {
		return Core::getModel("account/accounttype")->where("id", $id)->first();
	}

	/**
	 *	Get Fullname by account id
	 *	@var int $id
	 *	@var string $name
	 */
	public function getFullname( $id ) {
		$account = $this->where("id", $id)->first();
		return $account->fname . " " . $account->lname;
	}

	/**
	 *	Get account school year
	 *	@param int $id
	 *	@return string $scyear
	 */
	public function getSchoolYear( $id = false ) {
		$accountDataDb = Core::getModel("account/accountdata");
		if(! $id ) {
			$id = Core::getSingleton("system/session")->get("auth")->id;
		}
		$rs = $accountDataDb->where("account_id", $id)->first();
		if( $rs ) {
			return $rs->scyear;
		}
		return false;
	}

	/**
	 *	Get account semester
	 *	@param int $id
	 *	@return string $sem
	 */
	public function getSem( $id = false ) {
		$accountDataDb = Core::getModel("account/accountdata");
		if(! $id ) {
			$id = $id = Core::getSingleton("system/session")->get("auth")->id;
		}
		$rs = $accountDataDb->where("account_id", $id)->first();
		if( $rs ) {
			return $rs->sem;
		}
		return false;
	}

	/**
	 *	Create Account Automatically
	 */
	public function preAccount() {
		$hash = Core::getSingleton("system/hash");
		$data[] = [
			"account_type_id" 	=> 1,
			"fname" 			=> "Alexis",
			"lname" 			=> "Celis",
			"username" 			=> "alexis",
			"password" 			=> $hash->hash("blockman123"),
			"email" 			=> "alexis@alexis.com",
			"status"			=> self::STATUS_ACTIVE
		];

		$data[] = [
			"account_type_id" 	=> 2,
			"fname" 			=> "Alexis",
			"lname" 			=> "Celis",
			"username" 			=> "alexis1",
			"password" 			=> $hash->hash("blockman123"),
			"email" 			=> "alexis1@alexis.com",
			"status"			=> self::STATUS_ACTIVE
		];

		$data[] = [
			"account_type_id" 	=> 3,
			"fname" 			=> "Alexis",
			"lname" 			=> "Celis",
			"username" 			=> "alexis2",
			"password" 			=> $hash->hash("blockman123"),
			"email" 			=> "alexis2@alexis.com",
			"status"			=> self::STATUS_ACTIVE
		];

		foreach( $data as $dt ) {
			$this->insert($dt);
		}
		return true;
	}

}