//
//  SetPreferencesUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 26/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class SetPreferencesUV: UIViewController {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var genderView: UIView!
    @IBOutlet weak var genderLbl: MyLabel!
    @IBOutlet weak var genderChkBox: BEMCheckBox!
    @IBOutlet weak var genderViewHeight: NSLayoutConstraint!
    @IBOutlet weak var handiCapView: UIView!
    @IBOutlet weak var handiCapChkBox: BEMCheckBox!
    @IBOutlet weak var handiCapViewHeight: NSLayoutConstraint!
    @IBOutlet weak var handiCaplbl: MyLabel!
    
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
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)

        
        self.genderLbl.text = self.generalFunc.getLanguageLabel(origValue: "Prefer Female Driver", key: "LBL_ACCEPT_FEMALE_REQ_ONLY")
        self.genderLbl.fitText()
        
        self.handiCaplbl.text = self.generalFunc.getLanguageLabel(origValue: "Prefer Taxis with Handicap Accessibility", key: "LBL_MUST_HAVE_HANDICAP_ASS_CAR")
        self.handiCaplbl.fitText()
        
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
        
        genderChkBox.addTarget(self, action: #selector(self.genderChkBoxValueChanged), for: .valueChanged)
        self.genderChkBox.boxType = .square
        self.genderChkBox.offAnimationType = .bounce
        self.genderChkBox.onAnimationType = .bounce
        self.genderChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
        self.genderChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
        self.genderChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
        self.genderChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
        
        
        self.handiCapChkBox.boxType = .square
        self.handiCapChkBox.offAnimationType = .bounce
        self.handiCapChkBox.onAnimationType = .bounce
        self.handiCapChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
        self.handiCapChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
        self.handiCapChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
        self.handiCapChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
    }

    func genderChkBoxValueChanged(){
        print("Gender:\(genderChkBox.on)")
    }
}
