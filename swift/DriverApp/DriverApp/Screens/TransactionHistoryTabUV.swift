//
//  TransactionHistoryTabUV.swift
//  DriverApp
//
//  Created by NEW MAC on 18/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class TransactionHistoryTabUV: PageTabBarController, PageTabBarControllerDelegate {

    let generalFunc = GeneralFunctions()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
    }
    
    open override func prepare() {
        super.prepare()
        
        delegate = self
        preparePageTabBar()
        
        self.addBackBarBtn()
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Recent transaction", key: "LBL_RECENT_TRANSACTION")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Recent transaction", key: "LBL_RECENT_TRANSACTION")
    }
    
    fileprivate func preparePageTabBar() {
        pageTabBar.lineColor = Color.UCAColor.AppThemeColor
    }
    
    func pageTabBarController(pageTabBarController: PageTabBarController, didTransitionTo viewController: UIViewController) {
//        print("pageTabBarController", pageTabBarController, "didTransitionTo viewController:", viewController)
    }

}
