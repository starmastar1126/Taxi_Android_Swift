//
//  SetPreferencesUV.swift
//  DriverApp
//
//  Created by NEW MAC on 26/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class SetPreferencesUV: UIViewController, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var genderView: UIView!
    @IBOutlet weak var genderLbl: MyLabel!
    @IBOutlet weak var genderChkBox: BEMCheckBox!
    
    @IBOutlet weak var genderViewHeight: NSLayoutConstraint!
    @IBOutlet weak var handiCapView: UIView!
    @IBOutlet weak var handiCapViewHeight: NSLayoutConstraint!
    @IBOutlet weak var handiCaplbl: MyLabel!
    @IBOutlet weak var updateBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "SetPreferencesScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
    }

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Preferences", key: "LBL_PREFRANCE_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Preferences", key: "LBL_PREFRANCE_TXT")
        self.updateBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_UPDATE"))
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)

        self.genderLbl.text = self.generalFunc.getLanguageLabel(origValue: "Accept Female Only trip request", key: "LBL_ACCEPT_FEMALE_REQ_ONLY")
        
        let eFemaleOnlyReqAccept = userProfileJson.get("eFemaleOnlyReqAccept")
        
        let HANDICAP_ACCESSIBILITY_OPTION = userProfileJson.get("HANDICAP_ACCESSIBILITY_OPTION")
        let FEMALE_RIDE_REQ_ENABLE = userProfileJson.get("FEMALE_RIDE_REQ_ENABLE")
        
        if(HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES"){
            handiCapView.isHidden = true
            handiCapViewHeight.constant = 0
        }
        
        if(FEMALE_RIDE_REQ_ENABLE.uppercased() != "YES"){
            genderView.isHidden = true
            genderViewHeight.constant = 0
        }
        
        
        handiCapViewHeight.constant = 0
        handiCapView.isHidden = true
        
        self.genderChkBox.boxType = .square
        self.genderChkBox.offAnimationType = .bounce
        self.genderChkBox.onAnimationType = .bounce
        self.genderChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
        self.genderChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
        self.genderChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
        self.genderChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
        
        if(eFemaleOnlyReqAccept == "Yes"){
            self.genderChkBox.on = true
        }
        
        self.updateBtn.clickDelegate = self
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.updateBtn){
            updateData()
        }
    }
    
    func updateData(){
        let parameters = ["type":"updateuserPref","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType,"eFemaleOnly": "\(self.genderChkBox.on == true ? "Yes" : "No")"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "Your preferences updated successfully.", key: "LBL_PREF_SUCCESS_UPDATE"), uv: self)
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
    }
}
