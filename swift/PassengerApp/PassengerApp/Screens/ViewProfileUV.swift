//
//  ViewProfileUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 15/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ViewProfileUV: UIViewController, MyLabelClickDelegate {

    @IBOutlet weak var scrollViewContentView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var profilePicViewArea: UIView!
    @IBOutlet weak var editPicAreaView: UIView!
    @IBOutlet weak var placesSeperatorView: UIView!
    
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
    @IBOutlet weak var placeAreaTopMargin: NSLayoutConstraint!
    @IBOutlet weak var editIconImgView: UIImageView!
    
    @IBOutlet weak var placesHLbl: MyLabel!
    @IBOutlet weak var workLocContainerView: UIView!
    @IBOutlet weak var homeLocContainerView: UIView!
    @IBOutlet weak var homeLocImgView: UIImageView!
    @IBOutlet weak var homeLocVLbl: MyLabel!
    @IBOutlet weak var homeLocHLbl: MyLabel!
    @IBOutlet weak var homeLoceditImgContainerView: UIView!
    @IBOutlet weak var homeLocEditImgView: UIImageView!
    @IBOutlet weak var placesViewHeight: NSLayoutConstraint!
    @IBOutlet weak var placesContainerView: UIView!
    
    @IBOutlet weak var workLocVLbl: MyLabel!
    @IBOutlet weak var workLocHLbl: MyLabel!
    @IBOutlet weak var workLocImgView: UIImageView!
    @IBOutlet weak var workLoceditImgContainerView: UIView!
    @IBOutlet weak var workLocEditImgView: UIImageView!
    
    
    let generalFunc = GeneralFunctions()
    var containerViewHeight:CGFloat = 0
    
    var openImgSelection:OpenImageSelectionOption!
    
    var isFirstLaunch = true
    var cntView:UIView!

    var PAGE_HEIGHT:CGFloat = 610
    
    var homeLoc:CLLocation!
    var workLoc:CLLocation!
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        cntView = self.generalFunc.loadView(nibName: "ViewProfileScreenDesign", uv: self, contentView: scrollView)
        
        self.scrollView.addSubview(cntView)
        self.scrollView.bounces = false
        
        setContentSize()
        
        setData()
        
        let blurEffectView = UIVisualEffectView(effect: UIBlurEffect(style: UIBlurEffectStyle.dark))
        blurEffectView.frame = userProfilePicBgView.bounds
        blurEffectView.autoresizingMask = [.flexibleWidth, .flexibleHeight]
        self.userProfilePicBgView.addSubview(blurEffectView)
        
        self.placesSeperatorView.backgroundColor = UIColor.UCAColor.AppThemeColor
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isFirstLaunch == true){
            
            setContentSize()
            
            isFirstLaunch = false
        }
    }
    
    func setContentSize(){
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
        
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
    }

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROFILE_TITLE_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROFILE_TITLE_TXT")
        
        fNameTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FIRST_NAME_HEADER_TXT"))
        lNameTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LAST_NAME_HEADER_TXT"))
        emailTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_LBL_TXT"))
//        countryTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COUNTRY_TXT"))
        mobileTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MOBILE_NUMBER_HEADER_TXT"))
        languageTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LANGUAGE_TXT"))
        currencyTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CURRENCY_TXT"))
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        fNameTxtField.setText(text: userProfileJson.get("vName"))
        lNameTxtField.setText(text: userProfileJson.get("vLastName"))
        emailTxtField.setText(text: userProfileJson.get("vEmail"))
//        countryTxtField.setText(text: userProfileJson.get("vPhoneCode"))
        mobileTxtField.setText(text: "+\(userProfileJson.get("vPhoneCode"))\(userProfileJson.get("vPhone"))")
        languageTxtField.setText(text: userProfileJson.get("vName"))
        currencyTxtField.setText(text: userProfileJson.get("vCurrencyPassenger"))
        
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
//        countryTxtField.configDivider(isDividerEnabled: false)
        mobileTxtField.configDivider(isDividerEnabled: false)
        languageTxtField.configDivider(isDividerEnabled: false)
        currencyTxtField.configDivider(isDividerEnabled: false)
        
        fNameTxtField.configHighlighted(isHighLightedEnabled: true)
        lNameTxtField.configHighlighted(isHighLightedEnabled: true)
        emailTxtField.configHighlighted(isHighLightedEnabled: true)
//        countryTxtField.configHighlighted(isHighLightedEnabled: true)
        mobileTxtField.configHighlighted(isHighLightedEnabled: true)
        languageTxtField.configHighlighted(isHighLightedEnabled: true)
        currencyTxtField.configHighlighted(isHighLightedEnabled: true)

        
        userProfilePicBgImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImgName")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        usrProfileImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImgName")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        Utils.createRoundedView(view: usrProfileImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 2)
        setLanguage()
        
        editPicAreaView.backgroundColor = UIColor.UCAColor.AppThemeColor
        Utils.createRoundedView(view: editPicAreaView, borderColor: UIColor.clear, borderWidth: 0)
        
        GeneralFunctions.setImgTintColor(imgView: editIconImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        
        let userProfileImgTapGue = UITapGestureRecognizer()
        userProfileImgTapGue.addTarget(self, action: #selector(self.profilePicTapped))
        
        self.profilePicViewArea.isUserInteractionEnabled = true
        self.profilePicViewArea.addGestureRecognizer(userProfileImgTapGue)
        
        self.placesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PLACES_HEADER_TXT")
        
        if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.placesViewHeight.constant = 0
            self.placesContainerView.isHidden = true
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 165
            setContentSize()
        }
        
        checkPlaces()
        
    }
    
    
    func checkPlaces(){
        let userHomeLocationAddress = GeneralFunctions.getValue(key: "userHomeLocationAddress") != nil ? (GeneralFunctions.getValue(key: "userHomeLocationAddress") as! String) : ""
        let userWorkLocationAddress = GeneralFunctions.getValue(key: "userWorkLocationAddress") != nil ? (GeneralFunctions.getValue(key: "userWorkLocationAddress") as! String) : ""
        
        if(userHomeLocationAddress != ""){
            self.homeLocEditImgView.image = UIImage(named: "ic_edit")
            self.homeLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOME_PLACE")
            self.homeLocVLbl.text = GeneralFunctions.getValue(key: "userHomeLocationAddress") as? String
            
            let homeLatitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLatitude") as! String)
            let homeLongitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLongitude") as! String)
            
            self.homeLoc = CLLocation(latitude: homeLatitude, longitude: homeLongitude)
            
        }else{
            self.homeLocEditImgView.image = UIImage(named: "ic_add_plus")
            self.homeLocVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_HOME_PLACE_TXT")
            self.homeLocHLbl.text = "----"
        }
        
        if(userWorkLocationAddress != ""){
            self.workLocEditImgView.image = UIImage(named: "ic_edit")
            self.workLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WORK_PLACE")
            self.workLocVLbl.text = GeneralFunctions.getValue(key: "userWorkLocationAddress") as? String
            
            let workLatitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLatitude") as! String)
            let workLongitude = GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLongitude") as! String)
            
            self.workLoc = CLLocation(latitude: workLatitude, longitude: workLongitude)
        }else{
            self.workLocEditImgView.image = UIImage(named: "ic_add_plus")
            self.workLocVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_WORK_PLACE_TXT")
            self.workLocHLbl.text = "----"
        }
        
        GeneralFunctions.setImgTintColor(imgView: self.homeLocImgView, color: UIColor(hex: 0x353e45))
        GeneralFunctions.setImgTintColor(imgView: self.workLocImgView, color: UIColor(hex: 0x353e45))
        
        GeneralFunctions.setImgTintColor(imgView: self.homeLocEditImgView, color: UIColor(hex: 0x858f93))
        GeneralFunctions.setImgTintColor(imgView: self.workLocEditImgView, color: UIColor(hex: 0x858f93))
        
        let homePlaceTapGue = UITapGestureRecognizer()
        let workPlaceTapGue = UITapGestureRecognizer()
        
        homePlaceTapGue.addTarget(self, action: #selector(self.homePlaceEditTapped))
        workPlaceTapGue.addTarget(self, action: #selector(self.workPlaceEditTapped))
        
        self.homeLoceditImgContainerView.isUserInteractionEnabled = true
//        self.homeLoceditImgContainerView.addGestureRecognizer(homePlaceTapGue)
        self.homeLocContainerView.addGestureRecognizer(homePlaceTapGue)
        
        self.workLoceditImgContainerView.isUserInteractionEnabled = true
//        self.workLoceditImgContainerView.addGestureRecognizer(workPlaceTapGue)
        self.workLocContainerView.addGestureRecognizer(workPlaceTapGue)
    }
    
    func homePlaceEditTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = "HOME"
        addDestinationUv.centerLocation = homeLoc
        self.pushToNavController(uv: addDestinationUv)
    }
    
    func workPlaceEditTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = "WORK"
        addDestinationUv.centerLocation = workLoc
        self.pushToNavController(uv: addDestinationUv)
    }
    
    func profilePicTapped(){
        self.openImgSelection = OpenImageSelectionOption(uv: self)
        self.openImgSelection.show { (isImageUpload) in
            if(isImageUpload == true){
                self.setData()
            }
        }
        
    }
//    ChooseImageOptionView
    func setLanguage(){
        let dataArr = GeneralFunctions.getValue(key: Utils.LANGUAGE_LIST_KEY) as! NSArray
        
        for i in 0 ..< dataArr.count{
            let tempItem = dataArr[i] as! NSDictionary
            
            if((GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String) == tempItem.get("vCode")){
//                languageTxtField.setText(text: tempItem.get("vCode"))
                languageTxtField.setText(text: tempItem.get("vTitle"))

            }
        }
        
        if(dataArr.count < 2){
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 55
            self.langTxtFieldHeight.constant = 0.0
            self.languageTxtField.isHidden = true
            self.placeAreaTopMargin.constant = self.placeAreaTopMargin.constant - 5
        }
        
        setCurrency()
    }
    
    func setCurrency(){
        let dataArr = GeneralFunctions.getValue(key: Utils.CURRENCY_LIST_KEY) as! NSArray
        
        
        if(dataArr.count < 2){
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 55
            self.currencyTxtFieldHeight.constant = 0.0
            self.currencyTxtField.isHidden = true
            self.placeAreaTopMargin.constant = self.placeAreaTopMargin.constant - 5
        }
        
        setContentSize()
    }
    
    @IBAction func unwindToViewProfileScreen(_ segue:UIStoryboardSegue) {
        
        if(segue.source.isKind(of: AddDestinationUV.self))
        {
            
            let addDestinationUv = segue.source as! AddDestinationUV
            let selectedLocation = addDestinationUv.selectedLocation
            let selectedAddress = addDestinationUv.selectedAddress
            
            if(addDestinationUv.SCREEN_TYPE == "HOME"){
                GeneralFunctions.saveValue(key: "userHomeLocationAddress", value: selectedAddress as AnyObject)
                GeneralFunctions.saveValue(key: "userHomeLocationLatitude", value: ("\(selectedLocation!.coordinate.latitude)") as AnyObject)
                GeneralFunctions.saveValue(key: "userHomeLocationLongitude", value: ("\(selectedLocation!.coordinate.longitude)") as AnyObject)
            }else{
                GeneralFunctions.saveValue(key: "userWorkLocationAddress", value: selectedAddress as AnyObject)
                GeneralFunctions.saveValue(key: "userWorkLocationLatitude", value: ("\(selectedLocation!.coordinate.latitude)") as AnyObject)
                GeneralFunctions.saveValue(key: "userWorkLocationLongitude", value: ("\(selectedLocation!.coordinate.longitude)") as AnyObject)
            }
            
            checkPlaces()
        }
        
    }
    
    func myLableTapped(sender: MyLabel) {
        
    }

}
