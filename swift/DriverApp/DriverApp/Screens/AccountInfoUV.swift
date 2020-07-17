//
//  AccountInfoUV.swift
//  DriverApp
//
//  Created by NEW MAC on 23/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AccountInfoUV: UIViewController, MyBtnClickDelegate, MyTxtFieldClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var infoLbl: MyLabel!
    @IBOutlet weak var countryTxtField: MyTextField!
    
    @IBOutlet weak var emailAreaViewHeight: NSLayoutConstraint!
    @IBOutlet weak var mobileAreaViewHeight: NSLayoutConstraint!
    @IBOutlet weak var mobileNumArea: UIStackView!
    @IBOutlet weak var emailTxtField: MyTextField!
    @IBOutlet weak var mobileTxtField: MyTextField!
    @IBOutlet weak var continueBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    
    var isMobileEnabled = true
    var isEmailEnabled = true
    
    var isCountrySelected = false
    var selectedCountryCode = ""
    var selectedPhoneCode = ""
    
    var required_str = ""
    
    var isFirstLaunch = true
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "AccountInfoScreenDesign", uv: self, contentView: contentView))
        
        setData()
    }

    func setData(){
        required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Account Information", key: "LBL_ACC_INFO")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Account Information", key: "LBL_ACC_INFO")
        
        self.infoLbl.text = self.generalFunc.getLanguageLabel(origValue: "Please enter below information to continue", key: "LBL_ACC_SUB_INFO")
        self.infoLbl.fitText()
        
        self.emailTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_LBL_TXT"))
        self.countryTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COUNTRY_TXT"))
        self.mobileTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MOBILE_NUMBER_HEADER_TXT"))
        
        self.continueBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTINUE_BTN"))
        self.continueBtn.clickDelegate = self
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)

        if(userProfileJson.get("vEmail") != ""){
            emailAreaViewHeight.constant = 0
            emailTxtField.isHidden = true
            
            isEmailEnabled = false
            
            self.emailTxtField.setText(text: userProfileJson.get("vEmail"))
        }
        
        if(userProfileJson.get("vPhone") != ""){
            mobileAreaViewHeight.constant = 0
            mobileNumArea.isHidden = true
            self.mobileTxtField.setText(text: userProfileJson.get("vPhone"))
            
            isMobileEnabled = false
        }
        
        if(userProfileJson.get("vCode") != ""){
            isCountrySelected = true
            selectedCountryCode = userProfileJson.get("vCountry")
            selectedPhoneCode = userProfileJson.get("vCode")
        }else{
            if(GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_KEY) as! String) != "" && GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY) as! String) != "" && GeneralFunctions.getValue(key: Utils.DEFAULT_PHONE_CODE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DEFAULT_PHONE_CODE_KEY) as! String) != ""){
                self.selectedCountryCode = (GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY) as! String)
                self.selectedPhoneCode = (GeneralFunctions.getValue(key: Utils.DEFAULT_PHONE_CODE_KEY) as! String)
                //            self.countryTxtField.getTextField()!.text = "+" + selectedPhoneCode
                
                self.countryTxtField.setText(text: "+" + self.selectedPhoneCode)
                
                self.isCountrySelected = true
                self.countryTxtField.getTextField()!.sendActions(for: .editingChanged)
            }
        }
        
        
        self.countryTxtField.setEnable(isEnabled: false)
        self.countryTxtField.myTxtFieldDelegate = self
        
        self.mobileTxtField.getTextField()!.keyboardType = .numberPad
        
        let leftButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_trans")!, style: UIBarButtonItemStyle.plain, target: self, action: nil)
        self.navigationItem.leftBarButtonItem = leftButton
        
        
        let rightButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_nav_logout")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.logOutTapped))
        self.navigationItem.rightBarButtonItem = rightButton
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isFirstLaunch){
            countryTxtField.addArrowView(color: UIColor(hex: 0xbfbfbf), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))

            isFirstLaunch = false
        }
    }
    
    func logOutTapped(){
        let window = Application.window
        GeneralFunctions.logOutUser()
        GeneralFunctions.restartApp(window: window!)
    }
    
    func myTxtFieldTapped(sender: MyTextField) {
        if(sender == self.countryTxtField){
            let countryListUv = GeneralFunctions.instantiateViewController(pageName: "CountryListUV") as! CountryListUV
            countryListUv.fromAccountInfo = true
            self.pushToNavController(uv: countryListUv)
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.continueBtn){
            checkData()
        }
    }
    
    func checkData(){
        let mobileInvalid = generalFunc.getLanguageLabel(origValue: "Invalid mobile no.", key: "LBL_INVALID_MOBILE_NO")
        
        let emailEntered = Utils.checkText(textField: self.emailTxtField.getTextField()!) ? (GeneralFunctions.isValidEmail(testStr: Utils.getText(textField: self.emailTxtField.getTextField()!)) ? true : Utils.setErrorFields(textField: self.emailTxtField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_EMAIL_ERROR_TXT"))) : Utils.setErrorFields(textField: self.emailTxtField.getTextField()!, error: required_str)
//        let mobileEntered = Utils.checkText(textField: self.mobileTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.mobileTxtField.getTextField()!, error: required_str)

        let mobileEntered = Utils.checkText(textField: self.mobileTxtField.getTextField()!) ? (Utils.getText(textField: self.mobileTxtField.getTextField()!).characters.count >= Utils.minMobileLength ? true : Utils.setErrorFields(textField: self.mobileTxtField.getTextField()!, error: mobileInvalid)) : Utils.setErrorFields(textField: self.mobileTxtField.getTextField()!, error: required_str)
        
        let countryEntered = isCountrySelected ? true : Utils.setErrorFields(textField: self.countryTxtField.getTextField()!, error: required_str)
        
        
        if(((countryEntered == false || mobileEntered == false) && isMobileEnabled == true) || (emailEntered == false && isEmailEnabled == true)){
            return
        }
        if(GeneralFunctions.getValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY) as! String).uppercased() == "YES" && isMobileEnabled == true){
            checkUserExist()
        }else{
            updateProfile()
        }
    }
    func checkUserExist(){
        let parameters = ["type":"isUserExist","Email": isEmailEnabled == true ? Utils.getText(textField: self.emailTxtField.getTextField()!) : "", "Phone": isMobileEnabled == true ? Utils.getText(textField: self.mobileTxtField.getTextField()!) : "", "PhoneCode": isMobileEnabled == true ? self.selectedPhoneCode : ""]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    //                    LBL_CANCEL_TXT
                    DispatchQueue.main.async() {
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_VERIFY_MOBILE_CONFIRM_MSG"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedId) in
                            
                            if(btnClickedId == 0){
                                let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
                                accountVerificationUv.mobileNum = self.selectedPhoneCode + Utils.getText(textField: self.mobileTxtField.getTextField()!)
                                accountVerificationUv.isAccountInfo = true
                                accountVerificationUv.requestType = "DO_PHONE_VERIFY"
                                self.pushToNavController(uv: accountVerificationUv)
                            }
                        })
                    }
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func updateProfile(){
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)

        
        let parameters = ["type":"updateUserProfileDetail","vName": userProfileJson.get("vName"), "vLastName": userProfileJson.get("vLastName"), "vEmail": Utils.getText(textField: self.emailTxtField.getTextField()!), "vPhone": Utils.getText(textField: self.mobileTxtField.getTextField()!), "vPhoneCode": self.selectedPhoneCode, "vCountry": self.selectedCountryCode, "vDeviceType": Utils.deviceType, "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "LanguageCode": userProfileJson.get("vLang"), "CurrencyCode": userProfileJson.get("vCurrencyDriver")]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    _ = SetUserData(uv: self, userProfileJson: dataDict, isStoreUserId: false)
                    
                    let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
                    
                    let vCurrencyDriver = userProfileJson.get("vCurrencyDriver")
                    
                    if((GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String) != userProfileJson.get("vLang") || vCurrencyDriver != userProfileJson.get("vCurrencyDriver")){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"), content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NOTIFY_RESTART_APP_TO_CHANGE"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            let window = UIApplication.shared.delegate!.window!
                            
                            GeneralFunctions.restartApp(window: window!)
                        })
                        
                        return
                    }
                    
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    let window = UIApplication.shared.delegate!.window!
                    GeneralFunctions.restartApp(window: window!)
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    
    @IBAction func unwindToAccountInfo(_ segue:UIStoryboardSegue) {
        //        unwindToSignUp
        
        if(segue.source .isKind(of: CountryListUV.self))
        {
            
            let sourceViewController = segue.source as? CountryListUV
            let selectedPhoneCode:String = sourceViewController!.selectedCountryHolder!.vPhoneCode
            let selectedCountryCode = sourceViewController!.selectedCountryHolder!.vCountryCode
            
            self.selectedCountryCode = selectedCountryCode
            self.selectedPhoneCode = selectedPhoneCode
            
            self.countryTxtField.setText(text: "+" + selectedPhoneCode)
//            self.countryTxtField.getTextField()!.text = "+" + selectedPhoneCode
            self.isCountrySelected = true
            self.countryTxtField.getTextField()!.sendActions(for: .editingChanged)
            
        }else if(segue.source.isKind(of: AccountVerificationUV.self)){
            let accountVerificationUv = segue.source as! AccountVerificationUV
            
            if(accountVerificationUv.mobileNumVerified == true){
//                registerUser()
                updateProfile()
            }
        }
        
    }
}
