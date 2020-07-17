//
//  AppLoginUV.swift
//  DriverApp
//
//  Created by NEW MAC on 06/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import Crashlytics

class AppLoginUV: UIViewController, MyBtnClickDelegate, MyTxtFieldClickDelegate {

    @IBOutlet weak var registerBtn: MyButton!
    @IBOutlet weak var signInBtn: MyButton!
    @IBOutlet weak var bgImgView: UIImageView!
    
    @IBOutlet weak var languageSelectView: UIView!
    @IBOutlet weak var currencySelectView: UIView!
    @IBOutlet weak var lngLbl: MyLabel!
    @IBOutlet weak var currencyLbl: MyLabel!
    @IBOutlet weak var selectStackViewHeight: NSLayoutConstraint!
    @IBOutlet weak var selectStackViewBottomMargin: NSLayoutConstraint!

    @IBOutlet weak var currencyTxtField: MyTextField!
    @IBOutlet weak var languageTxtField: MyTextField!
    
    @IBOutlet weak var introSubLbl: MyLabel!
    
    var selectedCurrency = ""
    var selectedLngCode = ""
    
    var languageNameList = [String]()
    var languageCodes = [String]()
    
    var currenyList = [String]()

    let generalFunc = GeneralFunctions()
    
    var languageArrCount = 0
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.view.addSubview(self.generalFunc.loadView(nibName: "AppLoginScreenDesign", uv: self))
        
        self.bgImgView.image = Utils.appLoginImage()
        
//        self.bgImgView.backgroundColor = self.bgImgView.image!.getPixelColor(pos: CGPoint(x:20, y:10))
        
        registerBtn.clickDelegate = self
        signInBtn.clickDelegate = self
        
        setLabels()
        setData()
        
        self.lngLbl.text = GeneralFunctions.getValue(key: Utils.DEFAULT_LANGUAGE_TITLE_KEY) as? String
        self.currencyLbl.text = GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as? String
        
        setLanguage()
    }
    
    func setData(){
        currencyTxtField.allColor = UIColor.UCAColor.AppThemeTxtColor
        currencyTxtField.disableMenu()
        currencyTxtField.getTextField()!.clearButtonMode = .never
        currencyTxtField.setEnable(isEnabled: false)
        
        languageTxtField.allColor = UIColor.UCAColor.AppThemeTxtColor
        languageTxtField.disableMenu()
        languageTxtField.getTextField()!.clearButtonMode = .never
        languageTxtField.setEnable(isEnabled: false)
        
        languageTxtField.myTxtFieldDelegate = self
        currencyTxtField.myTxtFieldDelegate = self
    }

    func setLabels(){
        introSubLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOME_DRIVER_INTRO_DETAILS")
        introSubLbl.fitText()
        
        self.signInBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SIGN_IN_TXT"))
        self.registerBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SIGN_UP"))
        
        self.languageTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LANGUAGE_TXT"))
        self.currencyTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Currency", key: "LBL_CURRENCY_TXT"))
    }
    
    func myTxtFieldTapped(sender: MyTextField) {
        if(sender == self.languageTxtField){
            let openListView = OpenListView(uv: self, containerView: self.view)
            openListView.show(listObjects: languageNameList, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_LANGUAGE_HINT_TXT"), currentInst: openListView, handler: { (selectedItemId) in
                self.lngValueChanged(selectedItemId: selectedItemId)
            })
        }else if(sender == self.currencyTxtField){
            
            let openListView = OpenListView(uv: self, containerView: self.view)
            
            openListView.show(listObjects: currenyList, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_CURRENCY"), currentInst: openListView, handler: { (selectedItemId) in
                self.currencyValueChanged(selectedItemId: selectedItemId)
            })
        }
    }
   
    func setLanguage(){
        let dataArr = GeneralFunctions.getValue(key: Utils.LANGUAGE_LIST_KEY) as! NSArray
        self.languageArrCount = dataArr.count
        
        for i in 0 ..< dataArr.count{
            let tempItem = dataArr[i] as! NSDictionary
            
            if((GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String) == tempItem.get("vCode")){
                languageTxtField.setText(text: tempItem.get("vTitle"))
                self.selectedLngCode = tempItem.get("vCode")
            }
            
            languageNameList += [tempItem.get("vTitle")]
            languageCodes += [tempItem.get("vCode")]
            
        }
        
        if(dataArr.count < 2){
            languageSelectView.isHidden = true
        }
        
        setCurrency()
    }
    
    func lngValueChanged(selectedItemId:Int){
        self.selectedLngCode = self.languageCodes[selectedItemId]
        
        if((GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String) != self.selectedLngCode){
            changeLanguage()
        }
    }
    
    func changeLanguage(){
        let parameters = ["type":"changelanguagelabel","vLang": self.selectedLngCode]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    let window = UIApplication.shared.delegate!.window!
                    
                    GeneralFunctions.saveValue(key: Utils.languageLabelsKey, value: dataDict.getObj(Utils.message_str))
                    
                    
                    GeneralFunctions.saveValue(key: Utils.LANGUAGE_CODE_KEY, value: dataDict.get("vCode") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.LANGUAGE_IS_RTL_KEY, value: dataDict.get("eType") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.DEFAULT_LANGUAGE_TITLE_KEY, value: dataDict.get("vTitle") as AnyObject)
                    GeneralFunctions.saveValue(key: Utils.GOOGLE_MAP_LANGUAGE_CODE_KEY, value: dataDict.get("vGMapLangCode") as AnyObject)
                    Configurations.setAppLocal()
                    GeneralFunctions.restartApp(window: window!)
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func setCurrency(){
        let dataArr = GeneralFunctions.getValue(key: Utils.CURRENCY_LIST_KEY) as! NSArray
        
        for i in 0 ..< dataArr.count{
            let tempItem = dataArr[i] as! NSDictionary
            
            if((GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String) == tempItem.get("vName")){
                self.currencyTxtField.setText(text: tempItem.get("vName"))
                self.selectedCurrency = tempItem.get("vName")
            }
            currenyList += [tempItem.get("vName")]
        }
        
        if(dataArr.count < 2){
            currencySelectView.isHidden = true
            if(languageArrCount < 2){
                selectStackViewHeight.constant = 0
                selectStackViewBottomMargin.constant = 0
            }
        }
    }
    
    func currencyValueChanged(selectedItemId:Int){
        self.selectedCurrency = self.currenyList[selectedItemId]
        self.currencyLbl.text = self.selectedCurrency
        self.currencyTxtField.setText(text: self.selectedCurrency)
        GeneralFunctions.saveValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY, value: self.selectedCurrency as AnyObject)
    }

    func myBtnTapped(sender: MyButton) {

        if(sender == signInBtn){
            self.pushToNavController(uv: GeneralFunctions.instantiateViewController(pageName: "SignInUV"))
        }else if(sender == registerBtn){
            self.pushToNavController(uv: GeneralFunctions.instantiateViewController(pageName: "SignUpUV"))
        }
    }
}
