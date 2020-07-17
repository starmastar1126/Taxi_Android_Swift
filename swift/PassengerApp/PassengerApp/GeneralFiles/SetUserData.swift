//
//  SetUserData.swift
//  PassengerApp
//
//  Created by NEW MAC on 11/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class SetUserData: NSObject {

    var viewControlller:UIViewController!
    var userProfileJson:NSDictionary!
    var isStoreUserId = true
    let generalFunc = GeneralFunctions()
    
    init(uv: UIViewController, userProfileJson:NSDictionary, isStoreUserId:Bool) {
        self.viewControlller = uv
        self.userProfileJson = userProfileJson
        self.isStoreUserId = isStoreUserId
        super.init()
        
        setData()
    }
    
    
    
    func setData(){
        let isLanguageCodeChanged = self.userProfileJson!.get("changeLangCode")
        
        if(isLanguageCodeChanged == "Yes"){
            GeneralFunctions.saveValue(key: Utils.languageLabelsKey, value: self.userProfileJson!.getObj("UpdatedLanguageLabels"))
            GeneralFunctions.saveValue(key: Utils.LANGUAGE_CODE_KEY, value: self.userProfileJson!.get("vLanguageCode") as AnyObject)
            GeneralFunctions.saveValue(key: Utils.LANGUAGE_IS_RTL_KEY, value: self.userProfileJson!.get("langType") as AnyObject)
            GeneralFunctions.saveValue(key: Utils.GOOGLE_MAP_LANGUAGE_CODE_KEY, value: self.userProfileJson!.get("vGMapLangCode") as AnyObject)
            
            if(self.userProfileJson!.isKeyExist("LIST_LANGUAGES")){
                GeneralFunctions.saveValue(key: Utils.LANGUAGE_LIST_KEY, value: self.userProfileJson!.getArrObj("LIST_LANGUAGES"))
            }
            
            if(self.userProfileJson!.isKeyExist("LIST_CURRENCY")){
                GeneralFunctions.saveValue(key: Utils.CURRENCY_LIST_KEY, value: self.userProfileJson!.getArrObj("LIST_CURRENCY"))
            }
        }
        
        if(isStoreUserId){
            GeneralFunctions.saveValue(key: Utils.iMemberId_KEY, value: self.userProfileJson!.getObj(Utils.message_str).get("iUserId") as AnyObject)
            GeneralFunctions.saveValue(key: Utils.isUserLogIn, value: "1" as AnyObject)
        }
    }
}
