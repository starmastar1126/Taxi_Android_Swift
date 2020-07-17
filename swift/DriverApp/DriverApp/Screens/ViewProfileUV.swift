//
//  ViewProfileUV.swift
//  DriverApp
//
//  Created by NEW MAC on 15/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ViewProfileUV: UIViewController, MyLabelClickDelegate {

    @IBOutlet weak var scrollViewCOntentViewHeight: NSLayoutConstraint!
    @IBOutlet weak var scrollViewContentView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var profilePicViewArea: UIView!
    
    @IBOutlet weak var userProfilePicBgView: UIView!
    @IBOutlet weak var userProfilePicBgImgView: UIImageView!
    @IBOutlet weak var usrProfileImgView: UIImageView!
    @IBOutlet weak var fNameTxtField: MyTextField!
    @IBOutlet weak var lNameTxtField: MyTextField!
    @IBOutlet weak var emailTxtField: MyTextField!
//    @IBOutlet weak var countryTxtField: MyTextField!
    @IBOutlet weak var mobileTxtField: MyTextField!
    @IBOutlet weak var languageTxtField: MyTextField!
    @IBOutlet weak var currencyTxtField: MyTextField!
    @IBOutlet weak var langTxtFieldHeight: NSLayoutConstraint!
    @IBOutlet weak var currencyTxtFieldHeight: NSLayoutConstraint!
    @IBOutlet weak var serviceDesHLbl: MyLabel!
    @IBOutlet weak var serviceDesVLbl: MyLabel!
    @IBOutlet weak var editIconImgView: UIImageView!
    @IBOutlet weak var editPicAreaView: UIView!
    
    let generalFunc = GeneralFunctions()
    var containerViewHeight:CGFloat = 0
    
    var PAGE_HEIGHT:CGFloat = 450
    
    var openImgSelection:OpenImageSelectionOption!
    
    var isFirstLaunch = true
    
    var cntView:UIView!

    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        cntView = self.generalFunc.loadView(nibName: "ViewProfileScreenDesign", uv: self, contentView: scrollView)
        
        self.scrollView.addSubview(cntView)
        
        
        let blurEffectView = UIVisualEffectView(effect: UIBlurEffect(style: UIBlurEffectStyle.dark))
        blurEffectView.frame = userProfilePicBgView.bounds
        blurEffectView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
        self.userProfilePicBgView.addSubview(blurEffectView)
        
        setData()
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isFirstLaunch){
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: self.PAGE_HEIGHT)
            self.scrollView.bounces = false
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)
            
            isFirstLaunch = false
        }
    }

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROFILE_TITLE_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROFILE_TITLE_TXT")
        
        fNameTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FIRST_NAME_HEADER_TXT"))
        lNameTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LAST_NAME_HEADER_TXT"))
        emailTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_LBL_TXT"))
        mobileTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MOBILE_NUMBER_HEADER_TXT"))
        languageTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LANGUAGE_TXT"))
        currencyTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CURRENCY_TXT"))
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        fNameTxtField.setText(text: userProfileJson.get("vName"))
        lNameTxtField.setText(text: userProfileJson.get("vLastName"))
        emailTxtField.setText(text: userProfileJson.get("vEmail"))
//        countryTxtField.setText(text: userProfileJson.get("vCode"))
        mobileTxtField.setText(text: "+\(userProfileJson.get("vCode"))\(userProfileJson.get("vPhone"))")
        languageTxtField.setText(text: userProfileJson.get("vName"))
        currencyTxtField.setText(text: userProfileJson.get("vCurrencyDriver"))
        
        fNameTxtField.setEnable(isEnabled: false)
        lNameTxtField.setEnable(isEnabled: false)
        emailTxtField.setEnable(isEnabled: false)
//        countryTxtField.setEnable(isEnabled: false)
        mobileTxtField.setEnable(isEnabled: false)
        languageTxtField.setEnable(isEnabled: false)
        currencyTxtField.setEnable(isEnabled: false)
        
        fNameTxtField.configDivider(isDividerEnabled: false)
        lNameTxtField.configDivider(isDividerEnabled: false)
        emailTxtField.configDivider(isDividerEnabled: false)
        mobileTxtField.configDivider(isDividerEnabled: false)
        languageTxtField.configDivider(isDividerEnabled: false)
        currencyTxtField.configDivider(isDividerEnabled: false)
        
        fNameTxtField.configHighlighted(isHighLightedEnabled: true)
        lNameTxtField.configHighlighted(isHighLightedEnabled: true)
        emailTxtField.configHighlighted(isHighLightedEnabled: true)
        mobileTxtField.configHighlighted(isHighLightedEnabled: true)
        languageTxtField.configHighlighted(isHighLightedEnabled: true)
        currencyTxtField.configHighlighted(isHighLightedEnabled: true)
        
        userProfilePicBgImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        usrProfileImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        Utils.createRoundedView(view: usrProfileImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 2)
        setLanguage()
        
        editPicAreaView.backgroundColor = UIColor.UCAColor.AppThemeColor
        Utils.createRoundedView(view: editPicAreaView, borderColor: UIColor.clear, borderWidth: 0)
        
        GeneralFunctions.setImgTintColor(imgView: editIconImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        
        let userProfileImgTapGue = UITapGestureRecognizer()
        userProfileImgTapGue.addTarget(self, action: #selector(self.profilePicTapped))
        
        self.profilePicViewArea.isUserInteractionEnabled = true
        self.profilePicViewArea.addGestureRecognizer(userProfileImgTapGue)
        
        if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.PAGE_HEIGHT = 540
            self.serviceDesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SERVICE_DESCRIPTION")
            self.serviceDesHLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
            self.serviceDesHLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
            self.serviceDesHLbl.isHidden = false
            self.serviceDesVLbl.isHidden = false
            self.serviceDesVLbl.text = userProfileJson.get("tProfileDescription") == "" ? "--" : userProfileJson.get("tProfileDescription")
        }
        
    }
    
    func profilePicTapped(){
        self.openImgSelection = OpenImageSelectionOption(uv: self)
        self.openImgSelection.show { (isImageUpload) in
            if(isImageUpload == true){
                self.setData()
            }
        }
        
    }
    
    func setLanguage(){
        let dataArr = GeneralFunctions.getValue(key: Utils.LANGUAGE_LIST_KEY) as! NSArray
        
        for i in 0 ..< dataArr.count{
            let tempItem = dataArr[i] as! NSDictionary
            
            if((GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String) == tempItem.get("vCode")){
                languageTxtField.setText(text: tempItem.get("vTitle"))
            }
        }
        
        if(dataArr.count < 2){
            self.scrollViewCOntentViewHeight.constant = self.scrollViewCOntentViewHeight.constant - self.langTxtFieldHeight.constant
            self.langTxtFieldHeight.constant = 0.0
            self.languageTxtField.isHidden = true
        }
        
        setCurrency()
    }
    
    func setCurrency(){
        let dataArr = GeneralFunctions.getValue(key: Utils.CURRENCY_LIST_KEY) as! NSArray
        
        
        if(dataArr.count < 2){
            self.scrollViewCOntentViewHeight.constant = self.scrollViewCOntentViewHeight.constant - self.currencyTxtFieldHeight.constant
            self.currencyTxtFieldHeight.constant = 0.0
            self.currencyTxtField.isHidden = true
        }
        
    }
    
    func myLableTapped(sender: MyLabel) {
       
    }

}
