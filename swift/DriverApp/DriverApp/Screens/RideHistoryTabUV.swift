//
//  RideHistoryTabUV.swift
//  DriverApp
//
//  Created by NEW MAC on 17/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class RideHistoryTabUV: PageTabBarController, PageTabBarControllerDelegate {
    
    let generalFunc = GeneralFunctions()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    
    open override func prepare() {
        super.prepare()
        
        delegate = self
        preparePageTabBar()
        
        self.addBackBarBtn()
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS")
    }
    
    fileprivate func preparePageTabBar() {
        pageTabBar.lineColor = Color.UCAColor.AppThemeColor
    }
    
    func pageTabBarController(pageTabBarController: PageTabBarController, didTransitionTo viewController: UIViewController) {
//        print("pageTabBarController", pageTabBarController, "didTransitionTo viewController:", viewController)
    }
}
