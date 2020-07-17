//
//  ExtDouble.swift
//  PassengerApp
//
//  Created by NEW MAC on 31/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import Foundation

extension Double {
    /// Rounds the double to decimal places value
    func roundTo(places:Int) -> Double {
        let divisor = pow(10.0, Double(places))
        return (self * divisor).rounded() / divisor
    }
}
