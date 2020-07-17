//
//  InviteFriendsUV.swift
//  DriverApp
//
//  Created by NEW MAC on 13/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class InviteFriendsUV: UIViewController, MyBtnClickDelegate{

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var shareHLbl: MyLabel!
    @IBOutlet weak var inviteCodeLbl: MyLabel!
    @IBOutlet weak var descLbl: MyLabel!
    @IBOutlet weak var shareBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    var userProfileJson:NSDictionary!
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "InviteFriendsScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func setData(){
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)

        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_FRIEND_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_FRIEND_TXT")
        
        self.descLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_FRIEND_SHARE_TXT")
        self.shareHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_FRIEND_SHARE")
        
        self.shareBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_FRIEND_TXT"))
        self.inviteCodeLbl.text = userProfileJson.get("vRefCode")
        
        self.shareBtn.clickDelegate = self
        
        self.descLbl.fitText()
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.shareBtn){
//            let objectsToShare = ["\(self.generalFunc.getLanguageLabel(origValue: "", key: "SHARE_CONTENT")). \(self.generalFunc.getLanguageLabel(origValue: "", key: "MY_REFERAL_CODE")) \(inviteCodeLbl.text!)"]
            
            let objectsToShare = ["\(self.userProfileJson.get("INVITE_SHARE_CONTENT"))"]
            
            let activityVC = UIActivityViewController(activityItems: objectsToShare, applicationActivities: nil)
            activityVC.excludedActivityTypes = [UIActivityType.airDrop, UIActivityType.addToReadingList]
            activityVC.popoverPresentationController?.sourceView = self.view
            self.present(activityVC, animated: true, completion: nil)
        }
    }
}
