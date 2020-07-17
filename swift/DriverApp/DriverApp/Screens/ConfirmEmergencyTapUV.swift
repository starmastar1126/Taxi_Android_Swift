//
//  ConfirmEmergencyTapUV.swift
//  DriverApp
//
//  Created by NEW MAC on 19/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ConfirmEmergencyTapUV: UIViewController {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var topHLbl: MyLabel!
    @IBOutlet weak var bannerImgView: UIImageView!
    @IBOutlet weak var callPoliceView: UIView!
    @IBOutlet weak var callPoliceLbl: MyLabel!
    @IBOutlet weak var sendMsgView: UIView!
    @IBOutlet weak var sendMsgLbl: MyLabel!
    @IBOutlet weak var callImgView: UIImageView!
    @IBOutlet weak var msgImgView: UIImageView!
    
    let generalFunc = GeneralFunctions()
    
    var iTripId = ""
    
    let sendMsgViewTapGue = UITapGestureRecognizer()
    let callPoliceViewTapGue = UITapGestureRecognizer()
    
    var SITE_POLICE_CONTROL_NUMBER = ""
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
        self.navigationController?.navigationBar.layer.zPosition = -1
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        self.navigationController?.navigationBar.layer.zPosition = 1
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.contentView.addSubview(self.generalFunc.loadView(nibName: "ConfirmEmergencyTapScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        
        self.headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        GeneralFunctions.setImgTintColor(imgView: bannerImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        
        self.topHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONFIRM_EME_PAGE_TITLE")
        self.callPoliceLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CALL_POLICE")
        self.sendMsgLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SEND_ALERT_EME_CONTACT")
        
        callPoliceViewTapGue.addTarget(self, action: #selector(self.policeViewTapped))
        sendMsgViewTapGue.addTarget(self, action: #selector(self.sendMsgViewTapped))
        
        callPoliceView.isUserInteractionEnabled = true
        sendMsgView.isUserInteractionEnabled = true
        callPoliceView.addGestureRecognizer(callPoliceViewTapGue)
        sendMsgView.addGestureRecognizer(sendMsgViewTapGue)
        
        headerView.layer.shadowOpacity = 0.5
        //        headerView.layer.shadowRadius = 1.1
        headerView.layer.shadowOffset = CGSize(width: 0, height: 3)
        headerView.layer.shadowColor = UIColor(hex: 0xc0c0c1).cgColor
        headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        GeneralFunctions.setImgTintColor(imgView: self.callImgView, color: UIColor.UCAColor.AppThemeColor)
        GeneralFunctions.setImgTintColor(imgView: self.msgImgView, color: UIColor.UCAColor.AppThemeColor)
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)

        SITE_POLICE_CONTROL_NUMBER = userProfileJson.get("SITE_POLICE_CONTROL_NUMBER")
        
        setData()
    }
    

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMERGENCY_CONTACT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMERGENCY_CONTACT")
    }
    
    func policeViewTapped(){
        DispatchQueue.main.async {
            UIApplication.shared.openURL(URL(string:"telprompt:" + self.SITE_POLICE_CONTROL_NUMBER)!)
        }
    }
    
    func sendMsgViewTapped(){
        sendAlertToEmeContacts()
    }
    
    func sendAlertToEmeContacts(){
        let parameters = ["type":"sendAlertToEmergencyContacts","iUserId": GeneralFunctions.getMemberd(), "iTripId": iTripId, "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get(Utils.message_str) == "LBL_ADD_EME_CONTACTS"){
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        let emergencyContactsUv = GeneralFunctions.instantiateViewController(pageName: "EmergencyContactsUV") as! EmergencyContactsUV
                        self.pushToNavController(uv: emergencyContactsUv)
                    })
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }

}
