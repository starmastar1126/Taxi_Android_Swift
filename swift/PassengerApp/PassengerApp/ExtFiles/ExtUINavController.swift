//
//  ExtUINavController.swift
//  PassengerApp
//
//  Created by NEW MAC on 12/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation
extension UINavigationController {
    
    func backToViewController(vc: AnyClass) {
        // iterate to find the type of vc
        for element:UIViewController in viewControllers{
            if element.isKind(of: vc) {
                self.popToViewController(element, animated: true)
                break
            }
        }
    }
}
