//
//  AddPaymentUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 19/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AddPaymentUV: UIViewController, MyBtnClickDelegate {
    
    var PAGE_HEIGHT:CGFloat = 667
    
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var creditCardNumView: UIView!
    @IBOutlet weak var creditCardTxtField: MyTextField!
    @IBOutlet weak var expiryView: UIView!
    @IBOutlet weak var monthTxtField: MyTextField!
    @IBOutlet weak var yearTxtField: MyTextField!
    @IBOutlet weak var cvvView: UIView!
    @IBOutlet weak var cvvTxtField: MyTextField!
    @IBOutlet weak var configCardBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    
    var paymentUv:PaymentUV!
    
    var PAGE_MODE = "ADD"
    var isPageLoad = false
    
    var required_str = ""
    var invalid_str = ""
    
    var cntView:UIView!
    
    var isFromUFXPayMode = false
    
    var isFromMainScreen = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        self.addBackBarBtn()
        
        cntView = self.generalFunc.loadView(nibName: "AddPaymentScreenDesign", uv: self, contentView: scrollView)
        
        
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
        
        self.scrollView.addSubview(cntView)
        self.scrollView.bounces = false
        
        setData()
    }
    
    override func viewDidAppear(_ animated: Bool) {
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
//        if(isPageLoad == false){
//            cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
//            
//            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
//            
//            setData()
//            
//            isPageLoad = true
//        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: self.PAGE_MODE == "ADD" ? "LBL_ADD_CARD" : "LBL_CHANGE_CARD")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: self.PAGE_MODE == "ADD" ? "LBL_ADD_CARD" : "LBL_CHANGE_CARD")
        
        
        creditCardNumView.layer.shadowOpacity = 0.5
        creditCardNumView.layer.shadowOffset = CGSize(width: 0, height: 3)
        creditCardNumView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        expiryView.layer.shadowOpacity = 0.5
        expiryView.layer.shadowOffset = CGSize(width: 0, height: 3)
        expiryView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        cvvView.layer.shadowOpacity = 0.5
        cvvView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cvvView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        self.creditCardTxtField.textFieldType = "CARD"
        self.creditCardTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Card Number", key: "LBL_CARD_NUMBER_TXT"))
        self.monthTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EXP_MONTH_HINT_TXT"))
        self.yearTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EXP_YEAR_HINT_TXT"))
        self.cvvTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "CVV", key: "LBL_CVV"))
        
        self.cvvTxtField.maxCharacterLimit = 5
        self.creditCardTxtField.maxCharacterLimit = 20
        self.monthTxtField.maxCharacterLimit = 2
        self.yearTxtField.maxCharacterLimit = 4
        
        self.configCardBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: self.PAGE_MODE == "ADD" ? "LBL_ADD_CARD" : "LBL_CHANGE_CARD"))
        
        required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
        invalid_str =  self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVALID")
        
        self.configCardBtn.clickDelegate = self

        self.creditCardTxtField.getTextField()!.keyboardType = .numberPad
        self.monthTxtField.getTextField()!.keyboardType = .numberPad
        self.yearTxtField.getTextField()!.keyboardType = .numberPad
        self.cvvTxtField.getTextField()!.keyboardType = .numberPad
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.configCardBtn){
            checkData()
        }
    }
    func checkData(){
        
        let monthNum = Utils.getText(textField: self.monthTxtField.getTextField()!).isNumeric() ? GeneralFunctions.parseFloat(origValue: 0, data: Utils.getText(textField: self.monthTxtField.getTextField()!)) : 0
        
        let cardNoEntered = Utils.checkText(textField: creditCardTxtField.getTextField()!) ? (STPCardValidator.validationState(forNumber: Utils.getText(textField: self.creditCardTxtField.getTextField()!), validatingCardBrand: true) == .valid ? true : Utils.setErrorFields(textField: self.creditCardTxtField.getTextField()!, error: invalid_str)) : Utils.setErrorFields(textField: self.creditCardTxtField.getTextField()!, error: required_str)
        
        let monthEntered = Utils.checkText(textField: monthTxtField.getTextField()!) ? ((Utils.getText(textField: self.monthTxtField.getTextField()!).isNumeric() == false || Utils.getText(textField: self.monthTxtField.getTextField()!).characters.count < 2) ? Utils.setErrorFields(textField: self.monthTxtField.getTextField()!, error: invalid_str) : ( monthNum > 12 ? Utils.setErrorFields(textField: self.monthTxtField.getTextField()!, error: invalid_str) : true)) : Utils.setErrorFields(textField: self.monthTxtField.getTextField()!, error: required_str)

        let yearEntered = Utils.checkText(textField: yearTxtField.getTextField()!) ? ((Utils.getText(textField: self.yearTxtField.getTextField()!).isNumeric() == false || Utils.getText(textField: self.yearTxtField.getTextField()!).characters.count < 4 || Utils.getText(textField: self.yearTxtField.getTextField()!).characters.count > 4) ? Utils.setErrorFields(textField: self.yearTxtField.getTextField()!, error: invalid_str) : true) : Utils.setErrorFields(textField: self.yearTxtField.getTextField()!, error: required_str)
        
        let cvvEntered = Utils.checkText(textField: cvvTxtField.getTextField()!) ? ((Utils.getText(textField: self.cvvTxtField.getTextField()!).isNumeric() == false || Utils.getText(textField: self.cvvTxtField.getTextField()!).characters.count < 2 || Utils.getText(textField: self.cvvTxtField.getTextField()!).characters.count > 4) ? Utils.setErrorFields(textField: self.cvvTxtField.getTextField()!, error: invalid_str) : true) : Utils.setErrorFields(textField: self.cvvTxtField.getTextField()!, error: required_str)

        if (cardNoEntered == false || cvvEntered == false || monthEntered == false || yearEntered == false) {
            return;
        }
        
        DispatchQueue.main.async() {
            self.generateToken()
        }
    }
    
    func generateToken(){
        let cardParams = STPCardParams()
        cardParams.number = Utils.getText(textField: self.creditCardTxtField.getTextField()!)
        cardParams.expMonth = UInt(Int(Utils.getText(textField: self.monthTxtField.getTextField()!))!)
        cardParams.expYear = UInt(Int(Utils.getText(textField: self.yearTxtField.getTextField()!))!)
        cardParams.cvc = Utils.getText(textField: self.cvvTxtField.getTextField()!)
        
        
         let loadingDialog = NBMaterialLoadingDialog.showLoadingDialogWithText(self.contentView, isCancelable: false, message: (GeneralFunctions()).getLanguageLabel(origValue: "Loading", key: "LBL_LOADING_TXT"))
        
        STPAPIClient.shared().createToken(withCard: cardParams) { (token, error) in
            if error != nil {
                // show the error to the user
//                self.generalFunc.setError(uv: self)
                if let Msg = error?.localizedDescription{
                    let errorMsg = Msg.replace("\\", withString: "")
                    self.generalFunc.setError(uv: self, title: "", content:  errorMsg)
                }else{
                    self.generalFunc.setError(uv: self)
                }
            } else if let token = token {
                
                self.addTokenToServer(vStripeToken: token.tokenId)
            }
            
            loadingDialog.hideDialog()
        }
    }
    
    func addTokenToServer(vStripeToken:String){
        var maskedCreditCardNo = ""
        
        let creditCardNo = Utils.getText(textField: self.creditCardTxtField.getTextField()!)
        
        for i in 0 ..< creditCardNo.characters.count {
            if(i < ((creditCardNo.characters.count) - 4)){
                maskedCreditCardNo = maskedCreditCardNo + "X"
            }else{
                maskedCreditCardNo = maskedCreditCardNo + creditCardNo.charAt(i: i)
            }
        }
        
        let parameters = ["type":"GenerateCustomer","iUserId": GeneralFunctions.getMemberd(), "vStripeToken": vStripeToken, "UserType": Utils.appUserType, "CardNo": maskedCreditCardNo]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)

                    if(self.isFromUFXPayMode == true){
                        self.performSegue(withIdentifier: "unwindToUfxPayModeScreen", sender: self)
                    }else{
                        if(self.isFromMainScreen == true){
                            self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                        }else{
                            self.paymentUv!.setData()
                            self.closeCurrentScreen()
                        }
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }

}
